// Unified formatter for ANY answer object (myAnswer or correctAnswer)

export function formatAnswer(answer) {
  if (!answer) return '—';

  // TEXT
  if (answer.textAnswer != null) return answer.textAnswer;

  // NUMERIC
  if (answer.numericAnswer != null) return answer.numericAnswer.toString();

  // SINGLE SELECT (TEXT)
  if (answer.selectedChoiceText != null) return answer.selectedChoiceText;

  // MULTI SELECT TEXTS
  if (Array.isArray(answer.selectedChoiceTexts) && answer.selectedChoiceTexts.length > 0) {
    return answer.selectedChoiceTexts.join(', ');
  }

  // SORTED (TEXTS)
  if (Array.isArray(answer.orderedChoiceTexts) && answer.orderedChoiceTexts.length > 0) {
    return answer.orderedChoiceTexts.join(' → ');
  }

  return '—';
}

export function formatCorrectAnswer(correct) {
  if (!correct) return "—";

  const parts = [];

  if (correct.textAnswer) {
    parts.push(correct.textAnswer);
  }

  if (correct.numericAnswer !== null && correct.numericAnswer !== undefined) {
    parts.push(correct.numericAnswer.toString());
  }

  if (correct.selectedChoiceText) {
    parts.push(correct.selectedChoiceText);
  }

  if (Array.isArray(correct.selectedChoiceTexts) && correct.selectedChoiceTexts.length > 0) {
    correct.selectedChoiceTexts.forEach((t, i) => {
      parts.push(`${i + 1}) ${t}`);
    });
  }

  return parts.length > 0 ? parts.join("  ·  ") : "—";
}