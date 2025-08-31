// src/utils/myAnswers.js (nebo tam, kde to máš)
export function normalizeAnswersFromQuestions(questions = [], ctx = {}) {
  const out = [];
  const list = Array.isArray(questions) ? questions : [];

  const choiceMap = (q) => {
    const m = new Map();
    (q.choiceConstraint?.choices || []).forEach(c => m.set(c.id, c.text));
    return m;
  };

  const getAns = (q) => q?.myAnswer ?? q?.answer ?? null;

  const labelsForIds = (ids, map) =>
    (Array.isArray(ids) ? ids : []).map(id => map.get(id) ?? String(id));

  for (const q of list) {
    const a = getAns(q);
    if (!a) continue;

    let has = false, valueRaw = null, valueLabels = null, answerText = null;

    switch (q.type) {
      case 'single_select': {
        const id = a.selectedChoiceId ?? null;
        has = !!id;
        if (has) {
          const lbl = choiceMap(q).get(id) ?? String(id);
          valueRaw = id;
          valueLabels = lbl;
          answerText = lbl;
        }
        break;
      }
      case 'multi_select': {
        const ids = Array.isArray(a.selectedChoiceIds) ? a.selectedChoiceIds : [];
        has = ids.length > 0;
        if (has) {
          const labels = labelsForIds(ids, choiceMap(q));
          valueRaw = ids;
          valueLabels = labels;
          answerText = labels.join(', ');
        }
        break;
      }
      case 'sort': {
        const ids = Array.isArray(a.orderedChoiceIds) ? a.orderedChoiceIds : [];
        has = ids.length > 0;
        if (has) {
          const labels = labelsForIds(ids, choiceMap(q));
          valueRaw = ids;
          valueLabels = labels;
          // šipky pro přehledné zobrazení pořadí
          answerText = labels.join(' → ');
        }
        break;
      }
      case 'numeric': {
        const num = a.numericAnswer;
        // akceptuj 0; vyřaď null/undefined/NaN
        has = (num === 0 || Number.isFinite(num));
        if (has) {
          valueRaw = num;
          valueLabels = String(num);
          answerText = String(num);
        }
        break;
      }
      case 'text': {
        const txt = (a.textAnswer ?? '').toString().trim();
        has = txt.length > 0;
        if (has) {
          valueRaw = txt;
          valueLabels = txt;
          answerText = txt;
        }
        break;
      }
      default:
        has = false;
    }

    if (!has) continue;

    out.push({
      // kontext z challenge
      challengeId: ctx.challengeId ?? null,
      challengeName: ctx.challengeName ?? null,
      answeredAt: q.answeredAt ?? ctx.challengeAnsweredAt ?? null,

      // otázka
      questionId: q.id,
      questionText: q.text ?? '',
      type: q.type,

      // odpověď
      valueRaw,        // původní hodnota (ID/IDs/number/text)
      valueLabels,     // label(y) pro UI (u single string, u multi/sort pole)
      answerText,      // předpřipravený display string (pro rychlé vypsání)

      // stabilní klíč pro v-for
      id: `${ctx.challengeId || 'ch'}:${q.id}`,
    });
  }

  // nejnovější první
  out.sort((a, b) => (new Date(b.answeredAt || 0)) - (new Date(a.answeredAt || 0)));
  return out;
}