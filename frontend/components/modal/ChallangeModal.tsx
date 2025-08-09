// components/modal/ChallangeModal.tsx
"use client";

import React, { useEffect, useMemo, useState } from "react";
import { useSession } from "next-auth/react";

type Choice = {
  id: string;
  text: string;
  description?: string | null;
  image?: string | null;
};

type ChoiceConstraint = {
  choices: Choice[];
  minSelections?: number | null;
  maxSelections?: number | null;
} | null;

type NumericConstraint = {
  min?: number | null;
  max?: number | null;
} | null;

type Question = {
  id: string;
  text: string;
  type: "single_select" | "multi_select" | "text" | "sort" | "numeric";
  image: string | null;
  choiceConstraint?: ChoiceConstraint;
  numericConstraint?: NumericConstraint;
  // nové z API – nepoužíváme zde, ale nevadí
  answeredAt?: string | null;
  answer?: {
    textAnswer?: string | null;
    numericAnswer?: number | null;
    selectedChoiceId?: string | null;
    selectedChoiceIds?: string[] | null;
    orderedChoiceIds?: string[] | null;
  } | null;
};

type ChallengeDetail = {
  id: string;
  name: string;
  shortDescription: string;
  description: string;
  image: string | null;
  maxPoints?: number;
  addedAt: string;
  startsAt: string;
  expiresAt: string;
  answeredAt: string | null;
  isStarted: boolean;
  isExpired: boolean;
  isAnswered: boolean;
  isEvaluated: boolean;
  hintText?: string | null;
  hintImage?: string | null;
  questions: Question[];
};

type ChallengeModalProps = {
  challengeId: string;
  onClose: () => void;
  onSubmitSuccess: () => void; // zavolá se -> parent udělá refetch a zavře modal
  apiBase?: string; // default http://localhost:8080
};

const ChallangeModal: React.FC<ChallengeModalProps> = ({
  challengeId,
  onClose,
  onSubmitSuccess,
  apiBase = "http://localhost:8080",
}) => {
  const { data: session } = useSession();
  const token = (session as any)?.accessToken as string | undefined;

  const [loading, setLoading] = useState(false);
  const [challenge, setChallenge] = useState<ChallengeDetail | null>(null);
  const [error, setError] = useState<string | null>(null);

  // Lokální odpovědi
  const [textAnswer, setTextAnswer] = useState("");
  const [numericAnswer, setNumericAnswer] = useState<string>("");
  const [singleChoiceId, setSingleChoiceId] = useState<string | null>(null);
  const [multiChoiceIds, setMultiChoiceIds] = useState<string[]>([]);
  const [submitting, setSubmitting] = useState(false);

  // Načti detail challenge kvůli otázce/otázkám
  useEffect(() => {
    const load = async () => {
      if (!challengeId) return;
      setLoading(true);
      setError(null);
      try {
        const res = await fetch(`${apiBase}/api/challenges/${challengeId}`, {
          headers: {
            "Content-Type": "application/json",
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
          },
        });
        if (!res.ok) {
          const payload = await res.json().catch(() => ({}));
          throw new Error(payload?.detail || `Failed to load challenge (${res.status})`);
        }
        const data = (await res.json()) as ChallengeDetail;
        setChallenge(data);
      } catch (e: any) {
        setError(e?.message || "Nepodařilo se načíst detail výzvy.");
      } finally {
        setLoading(false);
      }
    };
    load();
  }, [challengeId, apiBase, token]);

  const question = useMemo(() => challenge?.questions?.[0], [challenge]);

  const isAnySelected = useMemo(() => {
    if (!question) return false;
    switch (question.type) {
      case "text":
        return textAnswer.trim().length > 0;
      case "numeric":
        return numericAnswer.trim().length > 0;
      case "single_select":
        return !!singleChoiceId;
      case "multi_select":
        return multiChoiceIds.length > 0;
      case "sort":
        // sort zatím neimplementováno
        return false;
      default:
        return false;
    }
  }, [question, textAnswer, numericAnswer, singleChoiceId, multiChoiceIds]);

  const toggleMulti = (id: string) => {
    setMultiChoiceIds((prev) =>
      prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]
    );
  };

  const handleSubmit = async () => {
    if (!token) {
      alert("Musíš být přihlášen.");
      return;
    }
    if (!question) return;

    const payload = {
      questionId: question.id,
      textAnswer: question.type === "text" ? textAnswer : null,
      numericAnswer: question.type === "numeric" ? Number(numericAnswer) : null,
      selectedChoiceId: question.type === "single_select" ? singleChoiceId : null,
      selectedChoiceIds: question.type === "multi_select" ? multiChoiceIds : null,
      orderedChoiceIds: null as string[] | null, // sort zatím neřešíme
    };

    try {
      setSubmitting(true);
      const res = await fetch(`${apiBase}/api/questions/answer`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(payload),
      });

      if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        console.error("❌ Chyba při odeslání:", err);
        alert("Chyba při odesílání odpovědi.");
        return;
      }

      // Úspěch -> rodič refetchne seznam a modal zavře
      onSubmitSuccess();
    } catch (e) {
      console.error("❌ Výjimka při odesílání:", e);
      alert("Došlo k chybě během komunikace se serverem.");
    } finally {
      setSubmitting(false);
    }
  };

  // UI
  if (loading) {
    return (
      <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div className="bg-white rounded-lg shadow p-6">Načítám…</div>
      </div>
    );
  }

  if (error || !challenge) {
    return (
      <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div className="bg-white rounded-lg shadow p-6">
          <p className="text-red-600">{error || "Výzva nebyla nalezena."}</p>
          <button
            onClick={onClose}
            className="mt-4 px-4 py-2 rounded bg-gray-800 text-white"
          >
            Zavřít
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div className="relative w-[95%] max-w-2xl">
        <button
          onClick={onClose}
          className="absolute top-4 right-4 z-10 text-charcoal text-2xl hover:scale-110 transition-transform duration-150"
        >
          ✖
        </button>

        <div className="bg-white p-8 rounded-lg shadow-lg max-h-[90vh] overflow-y-auto">
          <div className="flex flex-col pb-4">
            <h2 className="text-3xl font-bold text-vibrantCoral text-center">
              {challenge.name}
            </h2>
            <p className="text-base text-charcoal mt-4">{challenge.description}</p>
          </div>

          <hr className="border-0 h-[2px] bg-charcoal/10 my-4" />

          {question && (
            <div className="flex flex-col gap-4">
              <h4 className="text-xl font-semibold text-charcoal">{question.text}</h4>

              {challenge.hintText && (
                <p className="italic text-sm text-gray-600 bg-gray-50 p-2 border-l-4 border-vibrantCoral">
                  💡 {challenge.hintText}
                </p>
              )}

              {question.type === "text" && (
                <textarea
                  value={textAnswer}
                  onChange={(e) => setTextAnswer(e.target.value)}
                  className="w-full border p-2 rounded"
                  placeholder="Zadej odpověď…"
                />
              )}

              {question.type === "numeric" && (
                <input
                  type="number"
                  value={numericAnswer}
                  onChange={(e) => setNumericAnswer(e.target.value)}
                  className="w-full border p-2 rounded"
                  placeholder="Zadej číslo…"
                />
              )}

              {question.type === "single_select" &&
                question.choiceConstraint?.choices && (
                  <div className="flex flex-col gap-2">
                    {question.choiceConstraint.choices.map((c) => (
                      <label key={c.id} className="flex items-start gap-2 cursor-pointer">
                        <input
                          type="radio"
                          name="singleSelect"
                          checked={singleChoiceId === c.id}
                          onChange={() => setSingleChoiceId(c.id)}
                        />
                        <div>
                          <p className="font-semibold">{c.text}</p>
                          {c.description && (
                            <p className="text-sm text-gray-600">{c.description}</p>
                          )}
                        </div>
                      </label>
                    ))}
                  </div>
                )}

              {question.type === "multi_select" &&
                question.choiceConstraint?.choices && (
                  <div className="flex flex-col gap-2">
                    {question.choiceConstraint.choices.map((c) => (
                      <label key={c.id} className="flex items-start gap-2 cursor-pointer">
                        <input
                          type="checkbox"
                          checked={multiChoiceIds.includes(c.id)}
                          onChange={() => toggleMulti(c.id)}
                        />
                        <div>
                          <p className="font-semibold">{c.text}</p>
                          {c.description && (
                            <p className="text-sm text-gray-600">{c.description}</p>
                          )}
                        </div>
                      </label>
                    ))}
                  </div>
                )}

              {question.type === "sort" && (
                <p className="text-gray-500 italic">Typ „sort“ ještě není implementován.</p>
              )}
            </div>
          )}

          <div className="mt-6">
            <button
              type="button"
              onClick={isAnySelected ? handleSubmit : undefined}
              disabled={!isAnySelected || submitting}
              className={`w-full py-4 text-lg font-bold text-white text-center transition-all duration-200 ${
                isAnySelected && !submitting
                  ? "bg-vibrantCoral cursor-pointer"
                  : "bg-coolGray cursor-not-allowed"
              }`}
            >
              {submitting ? "Odesílám…" : "Submit answer"}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ChallangeModal;