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