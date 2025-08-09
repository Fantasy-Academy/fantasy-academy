'use client';

import React, { useEffect, useMemo, useState } from 'react';
import { useSession } from 'next-auth/react';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';

/** --- Typy podle OpenAPI --- */
type Choice = { id: string; text: string; description?: string | null; image?: string | null };

type ChoiceConstraint = {
  choices: Choice[];
  minSelections?: number | null;
  maxSelections?: number | null;
} | null;

type NumericConstraint = { min?: number | null; max?: number | null } | null;

type Answer = {
  textAnswer: string | null;
  numericAnswer: number | null;
  selectedChoiceId: string | null;
  selectedChoiceIds: string[] | null;
  orderedChoiceIds: string[] | null;
} | null;

type Question = {
  id: string;
  text: string;
  type: 'single_select' | 'multi_select' | 'text' | 'sort' | 'numeric';
  image: string | null;
  numericConstraint: NumericConstraint;
  choiceConstraint: ChoiceConstraint;
  answeredAt: string | null;
  answer: Answer;
};

type ChallengeListItem = {
  id: string;
  name: string;
  shortDescription: string;
  description: string;
  image: string | null;
  addedAt: string;
  startsAt: string;
  expiresAt: string;
  answeredAt: string | null;
  isStarted: boolean;
  isExpired: boolean;
  isAnswered: boolean;
  isEvaluated: boolean;
  maxPoints?: number;
};

type ChallengeDetail = {
  id: string;
  name: string;
  shortDescription: string;
  description: string;
  maxPoints?: number;
  image: string | null;
  addedAt: string;
  startsAt: string;
  expiresAt: string;
  answeredAt: string | null;
  isStarted: boolean;
  isExpired: boolean;
  isAnswered: boolean;
  isEvaluated: boolean;
  questions: Question[];
  hintText: string | null;
  hintImage: string | null;
};

/** --- Pomocné formátování odpovědi --- */
function mapChoiceIdsToText(ids: string[] | null | undefined, choices?: Choice[]): string[] {
  if (!ids || !choices) return [];
  const map = new Map(choices.map(c => [c.id, c.text]));
  return ids.map(id => map.get(id) ?? id);
}

function formatAnswer(q: Question): string {
  const a = q.answer;

  if (!a) return '—';

  // 1) text / numeric
  if (q.type === 'text' && a.textAnswer) return a.textAnswer;
  if (q.type === 'numeric' && a.numericAnswer !== null && a.numericAnswer !== undefined)
    return String(a.numericAnswer);

  // 2) single select
  if (q.type === 'single_select') {
    const text = mapChoiceIdsToText(
      a.selectedChoiceId ? [a.selectedChoiceId] : [],
      q.choiceConstraint?.choices
    )[0];
    return text || '—';
  }

  // 3) multi select
  if (q.type === 'multi_select') {
    const items = mapChoiceIdsToText(a.selectedChoiceIds ?? [], q.choiceConstraint?.choices);
    return items.length ? items.join(', ') : '—';
  }

  // 4) sort (seřazené)
  if (q.type === 'sort') {
    const items = mapChoiceIdsToText(a.orderedChoiceIds ?? [], q.choiceConstraint?.choices);
    return items.length ? items.join(' → ') : '—';
  }

  return '—';
}

/** --- Stránka --- */
const MyAnswersPage: React.FC = () => {
  const { data: session, status } = useSession();
  const accessToken = (session as any)?.accessToken as string | undefined;

  const [completed, setCompleted] = useState<ChallengeDetail[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError]     = useState<string | null>(null);

  // načti zodpovězené challenge + jejich detaily
  useEffect(() => {
    const load = async () => {
      if (!accessToken) return;
      setLoading(true);
      setError(null);

      try {
        // 1) seznam challenge
        const listRes = await fetch('http://localhost:8080/api/challenges', {
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${accessToken}`,
          },
        });

        if (!listRes.ok) {
          const err = await listRes.json().catch(() => ({}));
          throw new Error(err?.detail || `Failed to load challenges (${listRes.status})`);
        }

        const listPayload = await listRes.json();
        const list: ChallengeListItem[] = Array.isArray(listPayload)
          ? listPayload
          : (listPayload?.member || []);

        const completedIds = list.filter(c => c.isAnswered).map(c => c.id);

        // 2) detail pro každou zodpovězenou
        const detailResArr = await Promise.all(
          completedIds.map(id =>
            fetch(`http://localhost:8080/api/challenges/${id}`, {
              headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${accessToken}`,
              },
            })
          )
        );

        const failed = detailResArr.find(r => !r.ok);
        if (failed) {
          const err = await failed.json().catch(() => ({}));
          throw new Error(err?.detail || `Failed to load detail (${failed.status})`);
        }

        const details = (await Promise.all(detailResArr.map(r => r.json()))) as ChallengeDetail[];

        // jen pro jistotu filtrujeme otázky, které opravdu mají answer/answeredAt
        const normalized = details.map(d => ({
          ...d,
          questions: d.questions?.filter(q => q.answeredAt || q.answer) ?? [],
        }));

        setCompleted(normalized);
      } catch (e: any) {
        setError(e?.message || 'Nepodařilo se načíst odpovědi.');
      } finally {
        setLoading(false);
      }
    };

    if (status !== 'loading') load();
  }, [accessToken, status]);

  const totalAnswers = useMemo(
    () => completed.reduce((acc, ch) => acc + (ch.questions?.length ?? 0), 0),
    [completed]
  );

  return (
    <BackgroundWrapper>
      <div className="min-h-screen py-8 px-4">
        <div className="max-w-4xl mx-auto">
          <h1 className="font-bebasNeue text-5xl text-charcoal mb-2">My Answers</h1>
          <p className="text-coolGray mb-6">
            Count of your answeres: {totalAnswers}
          </p>

          {status === 'loading' && (
            <div className="p-4 text-gray-600">Verifing…</div>
          )}

          {loading && (
            <div className="p-4 text-gray-600">Načítám…</div>
          )}

          {error && (
            <div className="p-4 text-red-600 bg-red-50 border border-red-200 rounded">{error}</div>
          )}

          {!loading && !error && completed.length === 0 && (
            <div className="p-4 text-gray-600 bg-white rounded shadow">
              No answeres yet.
            </div>
          )}

          {!loading && !error && completed.map((ch) => (
            <div key={ch.id} className="bg-white rounded-lg shadow mb-6">
              <div className="px-5 py-4 border-b border-gray-100 flex items-center gap-4">
                {ch.image && (
                  <img
                    src={ch.image}
                    alt={ch.name}
                    className="w-12 h-12 rounded-lg object-cover shrink-0"
                  />
                )}
                <div>
                  <h2 className="text-xl font-semibold text-charcoal">{ch.name}</h2>
                  <p className="text-sm text-coolGray">
                    {ch.answeredAt ? `Answered at ${new Date(ch.answeredAt).toLocaleString()}` : '—'}
                  </p>
                </div>
                {typeof ch.maxPoints === 'number' && (
                  <span className="ml-auto inline-block text-sm px-2 py-1 rounded bg-gray-100">
                    Max {ch.maxPoints} pts
                  </span>
                )}
              </div>

              {/* otázky + odpovědi */}
              <div className="px-5 py-4">
                {ch.questions.length === 0 && (
                  <p className="text-sm text-gray-500">No answeres are present.</p>
                )}

                {ch.questions.map((q) => (
                  <div key={q.id} className="py-3 border-b last:border-b-0 border-gray-100">
                    <div className="mb-1 font-medium text-charcoal">{q.text}</div>
                    <div className="text-sm text-gray-700">
                      <span className="font-semibold mr-2">Your answer:</span>
                      {formatAnswer(q)}
                    </div>

                    {q.image && (
                      <img
                        src={q.image}
                        alt=""
                        className="mt-2 w-full max-w-md rounded-lg object-cover"
                      />
                    )}
                    {q.answeredAt && (
                      <div className="mt-1 text-xs text-coolGray">
                        answered at {new Date(q.answeredAt).toLocaleString()}
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </div>
          ))}
        </div>
      </div>
    </BackgroundWrapper>
  );
};

export default MyAnswersPage;