<template>
  <div v-if="show" @click="$emit('close')" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <section class="relative flex flex-col md:flex-row gap-4 md:gap-6 bg-white rounded-lg
         w-full max-w-[95vw] md:w-[1100px]
         max-h-[100vh] overflow-y-auto md:overflow-hidden" @click.stop>

      <!-- Close button top-right -->
      <button @click="$emit('close')" class="absolute top-3 right-3 z-50 text-charcoal hover:text-black transition
         bg-white/80 backdrop-blur-sm rounded-full w-8 h-8 flex items-center justify-center shadow cursor-pointer">
        ✕
      </button>
      <!-- Loading state -->
      <div v-if="!challenge">
        Loading…
      </div>

      <!-- challenge exists -->
      <div v-else class="flex flex-col md:flex-row w-full overflow-y-auto">
        <div class="flex flex-col gap-6 py-6 px-6 w-full md:w-[420px] flex-shrink-0
         order-1 md:order-none bg-white">
          <div class="px-3 py-1 bg-light-purple text-white rounded-full text-xs font-semibold w-fit">
            {{ challenge.maxPoints }}FAPs
          </div>
          <div>
            <img v-if="hintImgSrc" :src="hintImgSrc"
              class="w-full max-h-40 sm:max-h-52 md:max-h-48 object-contain rounded bg-dark-white" />
          </div>
          <div>
            <div class="bg-light-purple px-2 py-1 rounded text-white">
              <h2>Your Outcome:</h2>
              <p class="font-bold">
                {{ challenge.userPoints }} FAPs
              </p>
            </div>
            <!-- 📱 Results Collapse (mobile only) -->
            <div class="mt-2">

              <!-- 📱 Mobile toggle button -->
              <button
                class="md:hidden w-full py-2 px-3 rounded bg-dark-white text-sm font-medium text-blue-black flex justify-between items-center"
                @click="toggleResultsMobile">
                quick overview
                <span>{{ showResultsMobile ? '▲' : '▼' }}</span>
              </button>

              <!-- 🖥 Desktop: always visible | 📱 Mobile: collapsible -->
              <div :class="[
                'transition-all duration-300 overflow-hidden',
                showResultsMobile ? 'max-h-[1500px] mt-3' : 'max-h-0 md:max-h-none',
                'md:max-h-none'
              ]">

                <!-- ✨ původní obsah, beze změny formátu -->
                <div v-if="questions.every(q => !getPlayerAnswer(q))" class="mt-2 md:mt-0">
                  <span class="text-cool-gray">No answers yet!</span>
                </div>

                <div v-else class="md:mt-0">
                  <div v-for="q in questions" :key="q.id" class="mt-2">

                    <p v-if="getPlayerAnswer(q)">
                      <span :class="hasPoints(q) ? 'text-green-600 font-semibold' : 'text-vibrant-coral font-semibold'">
                        Your answer: {{ getPlayerAnswer(q) }}
                      </span>
                    </p>

                    <p v-if="formatCorrectAnswer(q) && challenge.isEvaluated">
                      <span :class="hasPoints(q) ? 'text-green-600 font-semibold' : 'text-vibrant-coral font-semibold'">
                        Correct: {{ formatCorrectAnswer(q) }}
                      </span>
                    </p>

                    <p v-else>
                      <span class="text-cool-gray">Waiting for evaluation...</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Description -->
        <!-- Description / Right side -->
        <div class="text-blue-black bg-dark-white py-6 px-6 rounded-r-lg
         flex flex-col w-full
         max-h-[80vh] md:max-h-[85vh]
         overflow-y-auto  <!-- ← SCROLL TADY -->
         order-2">
          <h1 class="font-bold text-lg">{{ challenge.name || 'Challenge' }}</h1>
          <p class="text-blue-black font-alexandria">
            <span class="hint-content" v-html="challenge.description"></span>
          </p>

          <hr class="my-4 text-light-purple" />

          <!-- Hint -->
          <div v-if="challenge.hintText || hintImgSrc" class="flex flex-col mb-2">
            <h1 class="font-bold">Hint</h1>
            <div v-if="challenge.hintText" class="prose prose-sm max-w-none hint-content" v-html="challenge.hintText">
            </div>
          </div>

          <!-- Answer form -->
          <div class="flex-1 pr-4">

            <!-- 🔥 Odpovídání na otázky (pokud není expired) -->
            <div v-for="q in questions" :key="q.id"
              class="mb-5 rounded-xl border border-charcoal/10 bg-white p-4 shadow-sm">

              <!-- Read-only (expired) -->
              <template v-if="isQuestionReadOnly(q)">
                <p class="font-semibold mb-2">{{ q.text }}</p>
                <p class="text-sm mb-1">
                  <strong>Your answer:</strong> {{ getPlayerAnswer(q) }}
                </p>
                <p class="text-sm">
                  <strong>Correct:</strong> {{ formatCorrectAnswer(q) }}
                </p>
              </template>

              <!-- ✏️ Editable Questions -->
              <template v-else>
                <QuestionSingleSelect v-if="q.type === 'single_select'" :question="toSingleSelectUI(q)"
                  v-model="answers[q.id]" />
                <QuestionMultiSelect v-else-if="q.type === 'multi_select'" :question="toMultiSelectUI(q)"
                  v-model="answers[q.id]" />
                <QuestionText v-else-if="q.type === 'text'" :question="{ id: q.id, text: q.text }"
                  v-model="answers[q.id]" />
                <QuestionNumeric v-else-if="q.type === 'numeric'" :question="toNumericUI(q)" v-model="answers[q.id]" />
                <QuestionSort v-else-if="q.type === 'sort'" :question="toSortUI(q)" v-model="sortModels[q.id]" />

                <!-- Errors -->
                <p v-if="qErrors[q.id]" class="text-vibrant-coral mt-1 text-sm font-medium">
                  {{ qErrors[q.id] }}
                </p>
              </template>
            </div>

            <!-- Submit -->
            <button v-if="!readOnly" @click="handleSubmit" :disabled="submitting" class="w-full mt-4 py-2 rounded-lg text-white font-semibold animate-gradient
         bg-[linear-gradient(270deg,var(--color-light-purple),var(--color-dark-purple),var(--color-vibrant-coral))]
         bg-[length:200%_200%] shadow-sm transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
              {{ submitting ? 'Submitting…' : 'Submit answers' }}
            </button>

          </div>
        </div>
      </div>

    </section>
  </div>
</template>

<style>
@import "@/assets/hint.css";
</style>

<script setup>
import { ref, reactive, onBeforeUnmount, onMounted, watch, computed } from "vue";
import { apiGetChallengeDetail, apiAnswerChallenge } from "@/api/challenges";
import { resolvedImage, onImgError } from "@/utils/imageHelpers";
import { formatStatisticAnswer } from "../utils/formatAnswers";

// question components
import QuestionSingleSelect from "@/components/questions/SingleSelectQuestion.vue";
import QuestionMultiSelect from "@/components/questions/MultiSelectQuestion.vue";
import QuestionNumeric from "@/components/questions/NumericQuestion.vue";
import QuestionText from "@/components/questions/TextQuestion.vue";
import QuestionSort from "@/components/questions/SortQuestion.vue";

const props = defineProps({
  show: { type: Boolean, default: false },
  challengeId: { type: String, required: false },
});
const emit = defineEmits(["close", "submitted"]);

const loading = ref(false);
const error = ref(null);
const challenge = ref(null);
const questions = ref([]); // API form
const answers = reactive({}); // map questionId -> model
const sortModels = reactive({}); // map questionId -> [{ id,label } ...] for UI sort
const qErrors = reactive({}); // map questionId -> error string
const submitting = ref(false);

const log = (...a) => console.log("[ChallengeModal]", ...a);
const err = (...a) => console.error("[ChallengeModal]", ...a);

// absolute URLs for images
const challengeImgSrc = computed(() => resolvedImage(challenge.value));
const hintImgSrc = computed(() =>
  challenge.value?.hintImage ? resolvedImage({ image: challenge.value.hintImage }) : null
);

/**
 * READ-ONLY LOGIC
 * - Global readOnly ONLY when challenge is expired.
 * - Previously answered questions REMAIN EDITABLE while not expired.
 */
const readOnly = computed(() => !!challenge.value && !!challenge.value.isExpired);

function isQuestionReadOnly(_q) {
  // Only lock questions when the whole challenge is expired.
  return readOnly.value;
}

/** UI mappers */
function toSingleSelectUI(q) {
  return {
    id: q.id,
    text: q.text,
    hint: null,
    options: (q.choiceConstraint?.choices || []).map((c) => ({
      id: c.id,
      label: c.text,
      value: c.id,
      description: c.description ?? null,
      image: c.image ? resolvedImage({ image: c.image }) : null,
    })),
  };
}

function toMultiSelectUI(q) {
  const minSel = q.choiceConstraint?.minSelections ?? null;
  const maxSel = q.choiceConstraint?.maxSelections ?? null;
  return {
    id: q.id,
    text: q.text,
    hint: minSel || maxSel ? hintForSelect(minSel, maxSel) : null,
    options: (q.choiceConstraint?.choices || []).map((c) => ({
      id: c.id,
      label: c.text,
      value: c.id,
      description: c.description ?? null,
      image: c.image ? resolvedImage({ image: c.image }) : null,
    })),
  };
}

function toNumericUI(q) {
  return {
    id: q.id,
    text: q.text,
    hint: rangeHint(q.numericConstraint),
    min: q.numericConstraint?.min ?? null,
    max: q.numericConstraint?.max ?? null,
  };
}

function toSortUI(q) {
  const opts = (q.choiceConstraint?.choices || []).map((c) => ({
    id: c.id,
    label: c.text,
    image: c.image ? resolvedImage({ image: c.image }) : null,
  }));
  return {
    id: q.id,
    text: q.text,
    hint: "Order the options (drag & drop).",
    options: opts,
  };
}

/** Hints */
function rangeHint(constraint) {
  if (!constraint) return null;
  const hasMin = typeof constraint.min === "number";
  const hasMax = typeof constraint.max === "number";
  if (hasMin && hasMax) return `Enter a number from ${constraint.min} to ${constraint.max}.`;
  if (hasMin) return `Enter a number ≥ ${constraint.min}.`;
  if (hasMax) return `Enter a number ≤ ${constraint.max}.`;
  return null;
}
function hintForSelect(minSel, maxSel) {
  if (minSel && maxSel) return `Select ${minSel}–${maxSel} options.`;
  if (minSel) return `Select at least ${minSel} option(s).`;
  if (maxSel) return `Select at most ${maxSel} option(s).`;
  return null;
}

// zoom in hint image
const zoomedImage = ref(null);
function openImage(src) {
  zoomedImage.value = src;
}
function closeImage() {
  zoomedImage.value = null;
}

/** Init models per question type */
function initAnswerModels(apiQuestions) {
  Object.keys(qErrors).forEach((k) => delete qErrors[k]);

  const getAns = (q) => q?.myAnswer ?? q?.answer ?? null;

  apiQuestions.forEach((q) => {
    const a = getAns(q);

    switch (q.type) {
      case "single_select": {
        const preset = a?.selectedChoiceId || null;
        answers[q.id] = preset;
        break;
      }
      case "multi_select": {
        const preset = Array.isArray(a?.selectedChoiceIds) ? a.selectedChoiceIds : [];
        answers[q.id] = preset;
        break;
      }
      case "text": {
        answers[q.id] = a?.textAnswer ?? "";
        break;
      }
      case "numeric": {
        const num = typeof a?.numericAnswer === "number" ? a.numericAnswer : null;
        answers[q.id] = num;
        break;
      }
      case "sort": {
        const base = (q.choiceConstraint?.choices || []).map((c) => c.id);
        const preset = Array.isArray(a?.orderedChoiceIds) ? a.orderedChoiceIds : base;
        answers[q.id] = preset.slice();

        const opts = (q.choiceConstraint?.choices || []).map((c) => ({ id: c.id, label: c.text }));
        const ordered = preset.map((id) => opts.find((o) => o.id === id)).filter(Boolean);
        sortModels[q.id] = ordered;
        break;
      }
      default:
        answers[q.id] = null;
    }
  });
}

/** Keep submit order in sync with UI sort */
watch(
  () => ({ ...sortModels }),
  () => {
    for (const qId of Object.keys(sortModels)) {
      const arr = sortModels[qId];
      if (Array.isArray(arr)) answers[qId] = arr.map((o) => o.id);
    }
  },
  { deep: true }
);

/** Validation — only for NOT expired (editable) questions */
function validateAnswers() {
  Object.keys(qErrors).forEach((k) => delete qErrors[k]);

  for (const q of questions.value) {
    if (isQuestionReadOnly(q)) continue;

    const val = answers[q.id];

    if (q.type === "single_select") {
      if (!val) {
        qErrors[q.id] = "Select an answer.";
        continue;
      }
    }

    if (q.type === "multi_select") {
      const arr = Array.isArray(val) ? val : [];
      const minSel = q.choiceConstraint?.minSelections ?? 1;
      const maxSel = q.choiceConstraint?.maxSelections ?? null;

      if (arr.length < minSel) {
        qErrors[q.id] = minSel === 1 ? "Select at least one option." : `Select at least ${minSel} options.`;
        continue;
      }
      if (maxSel != null && arr.length > maxSel) {
        qErrors[q.id] = `Select at most ${maxSel} options.`;
        continue;
      }
    }

    if (q.type === "text") {
      const str = (val ?? "").toString().trim();
      if (!str) {
        qErrors[q.id] = "Please enter a text answer.";
        continue;
      }
    }

    if (q.type === "numeric") {
      if (val === null || val === "" || Number.isNaN(Number(val))) {
        qErrors[q.id] = "Enter a numeric value.";
        continue;
      }
      const num = Number(val);
      const min = q.numericConstraint?.min ?? null;
      const max = q.numericConstraint?.max ?? null;
      if (min != null && num < min) {
        qErrors[q.id] = `The number must be ≥ ${min}.`;
        continue;
      }
      if (max != null && num > max) {
        qErrors[q.id] = `The number must be ≤ ${max}.`;
        continue;
      }
    }

    if (q.type === "sort") {
      const arr = Array.isArray(val) ? val : [];
      if (arr.length === 0) {
        qErrors[q.id] = "The order cannot be empty.";
        continue;
      }
    }
  }

  return Object.keys(qErrors).length === 0;
}

/** Read-only label helpers */
function choiceMap(q) {
  const map = new Map();
  (q.choiceConstraint?.choices || []).forEach((c) => map.set(c.id, c.text));
  return map;
}

function roSingleLabel(q) {
  const id = q.myAnswer?.selectedChoiceId ?? q.answer?.selectedChoiceId ?? null;
  if (!id) return null;
  return choiceMap(q).get(id) || id;
}

function roMultiLabels(q) {
  const ids = Array.isArray(q.myAnswer?.selectedChoiceIds)
    ? q.myAnswer.selectedChoiceIds
    : Array.isArray(q.answer?.selectedChoiceIds)
      ? q.answer.selectedChoiceIds
      : [];
  const map = choiceMap(q);
  return ids.map((id) => map.get(id) || id);
}

function roSortLabels(q) {
  const ids = Array.isArray(q.myAnswer?.orderedChoiceIds)
    ? q.myAnswer.orderedChoiceIds
    : Array.isArray(q.answer?.orderedChoiceIds)
      ? q.answer.orderedChoiceIds
      : [];
  const map = choiceMap(q);
  return ids.map((id) => map.get(id) || id);
}

/** Fetch challenge */
async function fetchChallenge() {
  if (!props.challengeId) {
    log("⚠️ fetchChallenge skipped: missing challengeId");
    return;
  }

  loading.value = true;
  error.value = null;

  try {
    const data = await apiGetChallengeDetail(props.challengeId);
    challenge.value = {
      id: data.id,
      name: data.name,
      description: data.description || "",
      hintText: data.hintText ?? null,
      hintImage: data.hintImage ?? null,
      maxPoints: data.maxPoints ?? 0,
      isExpired: !!data.isExpired,
      isAnswered: !!data.isAnswered,
      isEvaluated: !!data.isEvaluated,
      isStarted: !!data.isStarted,
      userPoints: data.myPoints ?? data.points ?? 0,
    };
    questions.value = Array.isArray(data.questions) ? data.questions : [];
    initAnswerModels(questions.value);
  } catch (e) {
    err("fetchChallenge failed:", e);
    error.value = e?.message || "Failed to load challenge.";
  } finally {
    loading.value = false;
  }
}

/** Submit (Answer.challenge schema) — send ALL editable (not expired) answers */
async function handleSubmit() {
  if (!challenge.value) return;
  if (readOnly.value) return;

  if (!validateAnswers()) return;

  const editableQuestions = questions.value.filter((q) => !isQuestionReadOnly(q));
  const payload = {
    challengeId: challenge.value.id,
    answers: editableQuestions.map((q) => {
      const model = answers[q.id];
      const answer = {
        textAnswer: null,
        numericAnswer: null,
        selectedChoiceId: null,
        selectedChoiceIds: null,
        orderedChoiceIds: null,
      };

      switch (q.type) {
        case "single_select":
          answer.selectedChoiceId = model || null;
          break;
        case "multi_select":
          answer.selectedChoiceIds = Array.isArray(model) ? model : [];
          break;
        case "text":
          answer.textAnswer = (model ?? "").toString();
          break;
        case "numeric":
          answer.numericAnswer = model === "" || model === null ? null : Number(model);
          break;
        case "sort":
          answer.orderedChoiceIds = Array.isArray(model) ? model : [];
          break;
      }

      return { questionId: q.id, answer };
    }),
  };

  submitting.value = true;
  try {
    await apiAnswerChallenge(payload);
    emit("submitted");
  } catch (e) {
    err("handleSubmit failed:", e);
  } finally {
    submitting.value = false;
  }
}

function formatCorrectAnswer(question) {
  const answer = question.correctAnswer;
  if (!answer) return null;

  if (answer.numericAnswer !== null && answer.numericAnswer !== undefined) {
    return answer.numericAnswer;
  }

  if (answer.textAnswer) {
    return answer.textAnswer;
  }

  if (answer.selectedChoiceId) {
    const choice = question.choiceConstraint?.choices.find((c) => c.id === answer.selectedChoiceId);
    return choice ? choice.text : answer.selectedChoiceId;
  }

  if (Array.isArray(answer.selectedChoiceIds) && answer.selectedChoiceIds.length > 0) {
    return answer.selectedChoiceIds
      .map((id) => {
        const choice = question.choiceConstraint?.choices.find((c) => c.id === id);
        return choice ? choice.text : id;
      })
      .join(", ");
  }

  return null;
}

// mount & watchers
onMounted(() => {
  window.addEventListener("keydown", onKeydown);
  if (props.show) fetchChallenge();
});
watch(
  () => props.show,
  (val) => {
    document.body.style.overflow = val ? "hidden" : "";
    if (val) fetchChallenge();
  }
);
watch(
  () => props.challengeId,
  (val, oldVal) => {
    if (props.show && val && val !== oldVal) fetchChallenge();
  }
);

onBeforeUnmount(() => {
  window.removeEventListener("keydown", onKeydown);
});

function onKeydown(e) {
  if (e.key === "Escape" && props.show) {
    emit("close");
  }
}

function getPlayerAnswer(q) {
  const a = q.myAnswer || q.answer;
  if (!a) return null;

  if (a.textAnswer) return a.textAnswer;
  if (a.numericAnswer !== null && a.numericAnswer !== undefined) return a.numericAnswer;
  if (a.selectedChoiceId) {
    const c = q.choiceConstraint?.choices.find(x => x.id === a.selectedChoiceId);
    return c ? c.text : a.selectedChoiceId;
  }
  if (Array.isArray(a.selectedChoiceIds)) {
    return a.selectedChoiceIds
      .map(id => q.choiceConstraint?.choices.find(c => c.id === id)?.text || id)
      .join(", ");
  }
  return "—";
}

function hasPoints(q) {
  // body za otázku po evaluaci
  return typeof q.points === "number" && q.points > 0;
}

const showResultsMobile = ref(false);
const toggleResultsMobile = () => {
  showResultsMobile.value = !showResultsMobile.value;
};

let scrollY = 0;
</script>