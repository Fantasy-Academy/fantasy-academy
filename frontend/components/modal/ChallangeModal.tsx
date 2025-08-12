// components/modal/ChallangeModal.tsx
"use client";

import React, { useEffect, useMemo, useRef, useState } from "react";
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
  answeredAt?: string | null;
  answer?: {
    textAnswer?: string | null;
    numericAnswer?: number | null;
    selectedChoiceId?: string | null;
    selectedChoiceIds?: string[] | null;
    orderedChoiceIds?: string[] | null;
  } | null;
};

type AnswerValue = {
  textAnswer?: string | null;
  numericAnswer?: number | null;
  selectedChoiceId?: string | null;
  selectedChoiceIds?: string[] | null;
  orderedChoiceIds?: string[] | null;
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
  onSubmitSuccess: () => void;
  apiBase?: string;
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
  const [answers, setAnswers] = useState<Record<string, AnswerValue>>({});
  const [submittingAll, setSubmittingAll] = useState(false);
  const collectorsRef = useRef<Record<string, () => AnswerValue>>({});

  useEffect(() => {
    const load = async () => {
      if (!challengeId) return;
      setLoading(true);
      setError(null);
      try {
        const res = await fetch(`${process.env.NEXT_PUBLIC_BACKEND_URL}/api/challenges/${challengeId}`, {
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

  useEffect(() => {
    if (!challenge) return;
    const initial: Record<string, AnswerValue> = {};
    challenge.questions.forEach((q) => {
      initial[q.id] = {
        textAnswer: q.answer?.textAnswer ?? null,
        numericAnswer: q.answer?.numericAnswer ?? null,
        selectedChoiceId: q.answer?.selectedChoiceId ?? null,
        selectedChoiceIds: q.answer?.selectedChoiceIds ?? [],
        orderedChoiceIds:
          q.type === "sort"
            ? q.answer?.orderedChoiceIds ?? q.choiceConstraint?.choices?.map((c) => c.id) ?? []
            : null,
      };
    });
    setAnswers(initial);
  }, [challenge]);

// Renders one question with controlled answer value
type QuestionBlockProps = {
  question: Question;
  value: AnswerValue;
  onChange: (value: AnswerValue) => void;
  registerCollector: (questionId: string, getter: () => AnswerValue) => void;
};

const QuestionBlock: React.FC<QuestionBlockProps> = ({
  question,
  value,
  onChange,
  registerCollector,
}) => {
  const [textAnswer, setTextAnswer] = useState("");
  const [numericAnswer, setNumericAnswer] = useState<string>("");
  const [singleChoiceId, setSingleChoiceId] = useState<string | null>(null);
  const [multiChoiceIds, setMultiChoiceIds] = useState<string[]>([]);
  const [orderedChoiceIds, setOrderedChoiceIds] = useState<string[]>([]);
  const [draggingId, setDraggingId] = useState<string | null>(null);
  const touchedRef = useRef(false);

  // Register a live getter so the parent can read the latest local value at submit time
  useEffect(() => {
    registerCollector(question.id, () => ({
      textAnswer,
      numericAnswer: numericAnswer.trim() === "" ? null : Number(numericAnswer),
      selectedChoiceId: singleChoiceId,
      selectedChoiceIds: multiChoiceIds,
      orderedChoiceIds,
    }));
  }, [registerCollector, question.id, textAnswer, numericAnswer, singleChoiceId, multiChoiceIds, orderedChoiceIds]);

  // Hydrate local inputs from parent `value` until the user interacts.
  useEffect(() => {
    if (!touchedRef.current) {
      setTextAnswer(value?.textAnswer ?? "");
      setNumericAnswer(
        value?.numericAnswer != null ? String(value.numericAnswer) : ""
      );
      setSingleChoiceId(value?.selectedChoiceId ?? null);
      setMultiChoiceIds(value?.selectedChoiceIds ?? []);

      if (question.type === "sort" && question.choiceConstraint?.choices) {
        const initial = value?.orderedChoiceIds?.length
          ? value.orderedChoiceIds!
          : question.choiceConstraint.choices.map((c) => c.id);
        setOrderedChoiceIds(initial);
      } else {
        setOrderedChoiceIds([]);
      }
    }
  }, [question.id, value]);

  // Reset touch state when switching questions
  useEffect(() => {
    touchedRef.current = false;
  }, [question.id]);

  const choiceById: Record<string, Choice> = useMemo(() => {
    const map: Record<string, Choice> = {};
    question.choiceConstraint?.choices?.forEach((c) => (map[c.id] = c));
    return map;
  }, [question]);

  // sort helpers
  const moveItem = (id: string, delta: number) => {
    setOrderedChoiceIds((prev) => {
      touchedRef.current = true;
      const idx = prev.indexOf(id);
      if (idx === -1) return prev;
      const newIdx = Math.max(0, Math.min(prev.length - 1, idx + delta));
      const copy = [...prev];
      const [item] = copy.splice(idx, 1);
      copy.splice(newIdx, 0, item);
      const next = copy;
      onChange({
        textAnswer,
        numericAnswer: numericAnswer.trim() === "" ? null : Number(numericAnswer),
        selectedChoiceId: singleChoiceId,
        selectedChoiceIds: multiChoiceIds,
        orderedChoiceIds: next,
      });
      return next;
    });
  };
  const onDragStart = (id: string) => setDraggingId(id);
  const onDragOver = (e: React.DragEvent<HTMLDivElement>) => e.preventDefault();
  const onDrop = (targetId: string) => {
    if (!draggingId || draggingId === targetId) return;
    setOrderedChoiceIds((prev) => {
      touchedRef.current = true;
      const from = prev.indexOf(draggingId);
      const to = prev.indexOf(targetId);
      if (from === -1 || to === -1) return prev;
      const copy = [...prev];
      const [moved] = copy.splice(from, 1);
      copy.splice(to, 0, moved);
      const next = copy;
      onChange({
        textAnswer,
        numericAnswer: numericAnswer.trim() === "" ? null : Number(numericAnswer),
        selectedChoiceId: singleChoiceId,
        selectedChoiceIds: multiChoiceIds,
        orderedChoiceIds: next,
      });
      return next;
    });
    setDraggingId(null);
  };

  return (
    <div className="flex flex-col gap-4">
      <h4 className="text-xl font-semibold text-charcoal">{question.text}</h4>

      {question.type === "text" && (
        <textarea
          value={textAnswer}
          onChange={(e) => {
            touchedRef.current = true;
            setTextAnswer(e.target.value);
          }}
          onBlur={() => {
            onChange({
              textAnswer,
              numericAnswer: numericAnswer.trim() === "" ? null : Number(numericAnswer),
              selectedChoiceId: singleChoiceId,
              selectedChoiceIds: multiChoiceIds,
              orderedChoiceIds,
            });
          }}
          onKeyDown={(e) => {
            if (e.key === "Enter" && !e.shiftKey) {
              e.preventDefault();
              (e.currentTarget as HTMLTextAreaElement).blur();
            }
          }}
          className="w-full border p-2 rounded"
          placeholder="Zadej odpověď…"
        />
      )}

      {question.type === "numeric" && (
        <input
          type="number"
          value={numericAnswer}
          onChange={(e) => {
            touchedRef.current = true;
            setNumericAnswer(e.target.value);
          }}
          onBlur={() => {
            onChange({
              textAnswer,
              numericAnswer: numericAnswer.trim() === "" ? null : Number(numericAnswer),
              selectedChoiceId: singleChoiceId,
              selectedChoiceIds: multiChoiceIds,
              orderedChoiceIds,
            });
          }}
          onKeyDown={(e) => {
            if (e.key === "Enter") {
              e.preventDefault();
              (e.currentTarget as HTMLInputElement).blur();
            }
          }}
          className="w-full border p-2 rounded"
          placeholder="Zadej číslo…"
        />
      )}

      {question.type === "single_select" &&
        question.choiceConstraint?.choices && (
          <div className="flex flex-col gap-2">
            {question.choiceConstraint.choices.map((c) => (
              <label
                key={c.id}
                className="flex items-start gap-2 cursor-pointer"
              >
                <input
                  type="radio"
                  name={`singleSelect-${question.id}`}
                  checked={singleChoiceId === c.id}
                  onChange={() => {
                    touchedRef.current = true;
                    setSingleChoiceId(c.id);
                    onChange({
                      textAnswer,
                      numericAnswer: numericAnswer.trim() === "" ? null : Number(numericAnswer),
                      selectedChoiceId: c.id,
                      selectedChoiceIds: multiChoiceIds,
                      orderedChoiceIds,
                    });
                  }}
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
              <label
                key={c.id}
                className="flex items-start gap-2 cursor-pointer"
              >
                <input
                  type="checkbox"
                  checked={multiChoiceIds.includes(c.id)}
                  onChange={() => {
                    touchedRef.current = true;
                    const next = multiChoiceIds.includes(c.id)
                      ? multiChoiceIds.filter((x) => x !== c.id)
                      : [...multiChoiceIds, c.id];
                    setMultiChoiceIds(next);
                    onChange({
                      textAnswer,
                      numericAnswer: numericAnswer.trim() === "" ? null : Number(numericAnswer),
                      selectedChoiceId: singleChoiceId,
                      selectedChoiceIds: next,
                      orderedChoiceIds,
                    });
                  }}
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

      {question.type === "sort" &&
        question.choiceConstraint?.choices &&
        orderedChoiceIds.length > 0 && (
          <div className="flex flex-col gap-2">
            <p className="text-sm text-coolGray">
              Sort correctly options down below ↑/↓.
            </p>
            <div className="flex flex-col gap-2">
              {orderedChoiceIds.map((id, idx) => {
                const c = choiceById[id];
                if (!c) return null;
                const isDragging = draggingId === id;
                return (
                  <div
                    key={id}
                    draggable
                    onDragStart={() => onDragStart(id)}
                    onDragOver={onDragOver}
                    onDrop={() => onDrop(id)}
                    className={`flex items-center justify-between gap-3 rounded border p-3 bg-white ${
                      isDragging ? "opacity-60" : "opacity-100"
                    }`}
                  >
                    <div className="flex items-center gap-3">
                      <span className="w-6 text-center font-bold text-charcoal">
                        {idx + 1}
                      </span>
                      <span
                        className="cursor-grab select-none"
                        title="Drag to reorder"
                      >
                        ⋮⋮
                      </span>
                      <div>
                        <p className="font-semibold">{c.text}</p>
                        {c.description && (
                          <p className="text-sm text-gray-600">{c.description}</p>
                        )}
                      </div>
                    </div>
                    <div className="flex items-center gap-2">
                      <button
                        type="button"
                        onClick={() => moveItem(id, -1)}
                        className="px-2 py-1 text-sm rounded bg-gray-100 hover:bg-gray-200"
                        aria-label="Move up"
                      >
                        ↑
                      </button>
                      <button
                        type="button"
                        onClick={() => moveItem(id, +1)}
                        className="px-2 py-1 text-sm rounded bg-gray-100 hover:bg-gray-200"
                        aria-label="Move down"
                      >
                        ↓
                      </button>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        )}
    </div>
  );
};

  // ✅ Early returns are AFTER all hooks
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
            <div
              className="text-base text-charcoal mt-4"
              dangerouslySetInnerHTML={{ __html: challenge.description }}
            />
          </div>

          <hr className="border-0 h-[2px] bg-charcoal/10 my-4" />
          {challenge.hintText && (
            <div
              className="mb-6 italic text-sm text-gray-600 bg-gray-50 p-3 border-l-4 border-vibrantCoral"
              dangerouslySetInnerHTML={{ __html: `💡 ${challenge.hintText}` }}
            />
          )}

          {challenge.questions.map((q) => (
            <div key={q.id} className="mb-6">
              <QuestionBlock
                question={q}
                value={answers[q.id] || {}}
                onChange={(val) => setAnswers((prev) => ({ ...prev, [q.id]: val }))}
                registerCollector={(questionId, getter) => { collectorsRef.current[questionId] = getter; }}
              />
              <hr className="border-0 h-[2px] bg-charcoal/10 my-4" />
            </div>
          ))}
          <div className="mt-8">
            <button
              type="button"
              onClick={async () => {
                if (!token) {
                  alert("Musíš být přihlášen.");
                  return;
                }
                const payload = {
                  challengeId: challenge.id,
                  answers: challenge.questions.map((q) => {
                    const v = (collectorsRef.current[q.id]?.() || answers[q.id] || {}) as AnswerValue;
                    const shaped = {
                      textAnswer: q.type === "text" ? (v.textAnswer ?? null) : null,
                      numericAnswer: q.type === "numeric" ? (v.numericAnswer ?? null) : null,
                      selectedChoiceId: q.type === "single_select" ? (v.selectedChoiceId ?? null) : null,
                      selectedChoiceIds: q.type === "multi_select" ? (v.selectedChoiceIds ?? null) : null,
                      orderedChoiceIds: q.type === "sort" ? (v.orderedChoiceIds ?? null) : null,
                    };
                    return { questionId: q.id, answer: shaped };
                  }),
                };
                try {
                  setSubmittingAll(true);
                  const res = await fetch(`${process.env.NEXT_PUBLIC_BACKEND_URL}/api/challenges/answer`, {
                    method: "PUT",
                    headers: {
                      "Content-Type": "application/json",
                      Authorization: `Bearer ${token}`,
                    },
                    body: JSON.stringify(payload),
                  });
                  if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    console.error("❌ Chyba při odeslání odpovědí:", err);
                    alert("Chyba při odesílání odpovědí.");
                    return;
                  }
                  onSubmitSuccess();
                } catch (e) {
                  console.error("❌ Výjimka při odeslání odpovědí:", e);
                  alert("Došlo k chybě během komunikace se serverem.");
                } finally {
                  setSubmittingAll(false);
                }
              }}
              disabled={submittingAll}
              className={`w-full py-4 text-lg font-bold text-white text-center transition-all duration-200 ${
                !submittingAll ? "bg-vibrantCoral cursor-pointer" : "bg-coolGray cursor-not-allowed"
              }`}
            >
              {submittingAll ? "Odesílám…" : "Submit answers"}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ChallangeModal;
