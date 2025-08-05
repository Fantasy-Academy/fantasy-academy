'use client';

import React, { useState, useEffect } from 'react';
import { useSearchParams } from 'next/navigation';
import { useSession } from 'next-auth/react';

interface ChallengeModalProps {
  title: string;
  description: string;
  duration: number;
  isCompleted: boolean;
  onClose: () => void;
}

interface Choice {
  id: string;
  text: string;
  description?: string | null;
  image?: string | null;
}

interface Question {
  id: string;
  text: string;
  type: 'text' | 'single_select' | 'multi_select' | 'numeric' | 'sort';
  image: string | null;
  choiceConstraint?: {
    choices: Choice[];
    minSelections?: number;
    maxSelections?: number;
  } | null;
  numericConstraint?: {
    min?: number | null;
    max?: number | null;
  } | null;
}

const ChallengeModal: React.FC<ChallengeModalProps> = ({ title, description, onClose }) => {
  const searchParams = useSearchParams();
  const id = searchParams.get('id');

  const [question, setQuestion] = useState<Question | null>(null);
  const [hintText, setHintText] = useState<string | null>(null);
  const [selectedChoices, setSelectedChoices] = useState<Record<string, boolean>>({});
  const [textAnswer, setTextAnswer] = useState('');
  const [numericAnswer, setNumericAnswer] = useState('');

  const { data: session } = useSession();
  const token = session?.accessToken;

  useEffect(() => {
    if (!id) return;

    const fetchChallenge = async () => {
      try {
        const res = await fetch(`http://localhost:8080/api/challenges/${id}`);
        const data = await res.json();

        if (data.questions?.length > 0) {
          setQuestion(data.questions[0]);
        }
        if (data.hintText) {
          setHintText(data.hintText);
        }
      } catch (err) {
        console.error('❌ Chyba při načítání challenge:', err);
      }
    };

    fetchChallenge();
  }, [id]);

  const isAnySelected =
    Object.values(selectedChoices).some(Boolean) || textAnswer !== '' || numericAnswer !== '';

  const handleSingleSelect = (choiceId: string) => {
    setSelectedChoices({ [choiceId]: true });
  };

  const handleMultiSelect = (choiceId: string) => {
    setSelectedChoices((prev) => ({
      ...prev,
      [choiceId]: !prev[choiceId],
    }));
  };

  const handleSubmit = async () => {
    if (!token) {
      alert('Musíš být přihlášen.');
      return;
    }

    if (!question) return;

    const body: any = {
      questionId: question.id,
      textAnswer: question.type === 'text' ? textAnswer : null,
      numericAnswer: question.type === 'numeric' ? Number(numericAnswer) : null,
      selectedChoiceId:
        question.type === 'single_select'
          ? Object.keys(selectedChoices).find((id) => selectedChoices[id])
          : null,
      selectedChoiceIds:
        question.type === 'multi_select'
          ? Object.keys(selectedChoices).filter((id) => selectedChoices[id])
          : null,
      orderedChoiceIds: null,
    };

    try {
      const res = await fetch('http://localhost:8080/api/questions/answer', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(body),
      });

      if (res.ok) {
        alert('Odpověď byla uložena.');
      } else {
        const err = await res.json();
        console.error('❌ Chyba při odeslání:', err);
        alert('Chyba při odeslání odpovědi.');
      }
    } catch (error) {
      console.error('❌ Výjimka při odesílání:', error);
      alert('Došlo k chybě během komunikace se serverem.');
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
      <div className="relative w-[95%] max-w-2xl">
        {/* Zavírací tlačítko pevně vůči modal boxu */}
        <button
          onClick={onClose}
          className="absolute top-4 right-4 z-10 text-charcoal text-2xl hover:scale-110 transition-transform duration-[140ms]"
        >
          ✖
        </button>

        {/* Obsah modalu */}
        <div className="bg-darkWhite p-8 rounded-lg shadow-lg max-h-[90vh] overflow-y-auto">
          <div className="flex flex-col pb-4">
            <h2 className="text-5xl font-bold text-vibrantCoral text-center">{title}</h2>
            <h3 className="font-bebasNeue text-4xl text-charcoal mt-10">Coach&apos;s Corner</h3>
            <p className="text-lg text-charcoal">{description}</p>
          </div>

          <hr className="border-0 h-[2px] bg-charcoal my-4" />

          {question && (
            <div className="flex flex-col gap-4">
              <h4 className="text-2xl font-semibold text-charcoal">{question.text}</h4>
              {question.image && <img src={question.image} alt="question" className="max-h-64" />}
              {hintText && (
                <p className="italic text-md text-gray-600 bg-white p-2 border-l-4 border-vibrantCoral">
                  💡 {hintText}
                </p>
              )}

              {question.type === 'text' && (
                <textarea
                  value={textAnswer}
                  onChange={(e) => setTextAnswer(e.target.value)}
                  className="w-full border p-2"
                  placeholder="Zadej odpověď"
                />
              )}

              {question.type === 'numeric' && (
                <input
                  type="number"
                  value={numericAnswer}
                  onChange={(e) => setNumericAnswer(e.target.value)}
                  className="w-full border p-2"
                  placeholder="Zadej číslo"
                />
              )}

              {question.type === 'single_select' && question.choiceConstraint?.choices && (
                <div className="flex flex-col gap-2">
                  {question.choiceConstraint.choices.map((choice) => (
                    <label key={choice.id} className="flex items-start gap-2 cursor-pointer">
                      <input
                        type="radio"
                        name="singleSelect"
                        checked={!!selectedChoices[choice.id]}
                        onChange={() => handleSingleSelect(choice.id)}
                      />
                      <div>
                        <p className="font-semibold">{choice.text}</p>
                        {choice.description && (
                          <p className="text-sm text-gray-600">{choice.description}</p>
                        )}
                        {choice.image && (
                          <img src={choice.image} alt="choice" className="max-h-24 mt-1" />
                        )}
                      </div>
                    </label>
                  ))}
                </div>
              )}

              {question.type === 'multi_select' && question.choiceConstraint?.choices && (
                <div className="flex flex-col gap-2">
                  {question.choiceConstraint.choices.map((choice) => (
                    <label key={choice.id} className="flex items-start gap-2 cursor-pointer">
                      <input
                        type="checkbox"
                        checked={!!selectedChoices[choice.id]}
                        onChange={() => handleMultiSelect(choice.id)}
                      />
                      <div>
                        <p className="font-semibold">{choice.text}</p>
                        {choice.description && (
                          <p className="text-sm text-gray-600">{choice.description}</p>
                        )}
                        {choice.image && (
                          <img src={choice.image} alt="choice" className="max-h-24 mt-1" />
                        )}
                      </div>
                    </label>
                  ))}
                </div>
              )}

              {question.type === 'sort' && (
                <p className="text-gray-500 italic">Typ "sort" ještě není implementován.</p>
              )}
            </div>
          )}

          <div className="mt-6">
            <button
              type="button"
              onClick={isAnySelected ? handleSubmit : undefined}
              disabled={!isAnySelected}
              className={`
                w-full py-4 text-lg font-bold text-white text-center
                transition-all duration-200 font-sourceSans3
                ${isAnySelected ? 'bg-vibrantCoral cursor-pointer' : 'bg-coolGray cursor-not-allowed'}
              `}
            >
              Submit answer
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ChallengeModal;