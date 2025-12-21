<template>
  <div v-if="show" @click="$emit('close')" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="w-full max-w-2xl rounded-2xl bg-white shadow-main border border-charcoal/10" @click.stop>
      <!-- Header -->
      <header
        class="flex items-center justify-between rounded-t-2xl px-5 py-4 bg-gradient-to-r from-blue-black to-charcoal text-dark-white">
        <h2 class="text-xl font-bebas-neue tracking-wide">
          {{ challenge?.name || 'Challenge' }}
        </h2>
        <button class="text-dark-white/70 hover:text-golden-yellow transition" @click="$emit('close')">✕</button>
      </header>

      <!-- Body -->
      <section class="max-h-[70vh] overflow-y-auto px-5 py-4 bg-dark-white/30">
        <!-- States -->
        <div v-if="loading" class="text-cool-gray">Loading challenge…</div>
        <div v-else-if="error"
          class="rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-3 text-vibrant-coral">
          {{ error }}
        </div>

        <template v-else-if="challenge">
          <!-- Readonly info banner (ONLY when expired) -->
          <div v-if="readOnly"
            class="mb-4 rounded-xl border border-amber-300 bg-amber-50 p-3 text-amber-800 text-sm font-medium">
            This challenge has expired. Answers are read-only.
          </div>

          <!-- Main challenge image (optional) -->
          <img v-if="challengeImgSrc" :src="challengeImgSrc" :alt="challenge.name"
            class="mb-3 max-h-56 w-full rounded-xl object-cover border border-charcoal/10" @error="onImgError" />

          <!-- Description -->
          <p class="mb-4 text-blue-black font-alexandria">
            <span class="hint-content" v-html="challenge.description"></span>
          </p>

          <!-- Hint -->
          <div v-if="challenge.hintText || hintImgSrc"
            class="mb-5 rounded-xl border border-charcoal/10 bg-white p-3 text-sm shadow-sm">
            <div v-if="challenge.hintText" class="prose prose-sm max-w-none hint-content" v-html="challenge.hintText">
            </div>
            <img v-if="hintImgSrc" :src="hintImgSrc" alt="Hint"
              class="max-h-48 w-full rounded object-contain bg-dark-white cursor-zoom-in" @error="onImgError"
              @click="openImage(hintImgSrc)" />
          </div>

          <!-- Questions -->
          <div v-for="q in questions" :key="q.id"
            class="mb-5 rounded-xl border border-charcoal/10 bg-white p-4 shadow-sm">
            <!-- READ-ONLY block (only when challenge expired) -->
            <template v-if="isQuestionReadOnly(q)">
              <p class="mb-2 font-semibold text-blue-black font-alexandria">{{ q.text }}</p>

              <div class="text-sm text-blue-black">
                <!-- single_select -->
                <template v-if="q.type === 'single_select'">
                  <span class="inline-flex items-center rounded bg-dark-white px-2 py-1">
                    {{ roSingleLabel(q) || '—' }}
                  </span>
                </template>

                <!-- multi_select -->
                <template v-else-if="q.type === 'multi_select'">
                  <div class="flex flex-wrap gap-2">
                    <span v-for="(lbl, i) in roMultiLabels(q)" :key="i"
                      class="inline-flex items-center rounded bg-dark-white px-2 py-1">
                      {{ lbl }}
                    </span>
                    <span v-if="roMultiLabels(q).length === 0">—</span>
                  </div>
                </template>

                <!-- text -->
                <template v-else-if="q.type === 'text'">
                  <p class="whitespace-pre-line bg-dark-white rounded p-2">
                    {{ (q.answer?.textAnswer ?? '').trim() || '—' }}
                  </p>
                </template>

                <!-- numeric -->
                <template v-else-if="q.type === 'numeric'">
                  <span class="inline-flex items-center rounded bg-dark-white px-2 py-1">
                    {{ q.answer?.numericAnswer ?? '—' }}
                  </span>
                </template>

                <!-- sort -->
                <template v-else-if="q.type === 'sort'">
                  <ol class="list-decimal pl-5 space-y-1">
                    <li v-for="(lbl, i) in roSortLabels(q)" :key="i">
                      {{ lbl }}
                    </li>
                    <li v-if="roSortLabels(q).length === 0">—</li>
                  </ol>
                </template>

                <template v-else>
                  <span class="text-cool-gray">Unknown question type: {{ q.type }}</span>
                </template>
              </div>

              <div class="mt-5">
                <p v-if="q.answeredAt" class="mt-2 text-xs text-cool-gray">
                  Answered at: {{ new Date(q.answeredAt).toLocaleString() }}
                </p>
                <p v-if="challenge.isExpired">
                  Correct answer:
                  <strong>{{ formatCorrectAnswer(q) }}</strong>
                </p>
              </div>
            </template>

            <!-- EDITABLE block (always editable while NOT expired) -->
            <template v-else>
              <!-- single_select -->
              <QuestionSingleSelect v-if="q.type === 'single_select'" :question="toSingleSelectUI(q)"
                v-model="answers[q.id]" />

              <!-- multi_select -->
              <QuestionMultiSelect v-else-if="q.type === 'multi_select'" :question="toMultiSelectUI(q)"
                v-model="answers[q.id]" />

              <!-- text -->
              <QuestionText v-else-if="q.type === 'text'" :question="{ id: q.id, text: q.text, hint: null }"
                v-model="answers[q.id]" />

              <!-- numeric -->
              <QuestionNumeric v-else-if="q.type === 'numeric'" :question="toNumericUI(q)" v-model="answers[q.id]" />

              <!-- sort -->
              <QuestionSort v-else-if="q.type === 'sort'" :question="toSortUI(q)" v-model="sortModels[q.id]" />

              <div v-else class="text-sm text-cool-gray">Unknown question type: {{ q.type }}</div>

              <!-- Per-question validation error -->
              <p v-if="qErrors[q.id]" class="mt-2 text-sm text-vibrant-coral font-medium">
                {{ qErrors[q.id] }}
              </p>
            </template>

            <!-- ALWAYS SHOW STATISTICS (ACTIVE, NON-ANSWERED, ANSWERED, EXPIRED) -->
            <div v-if="q.statistics" class="mt-4 rounded-lg border border-charcoal/10 bg-dark-white/60 p-3">
              <p class="text-sm font-semibold text-blue-black mb-2">
                Player answer distribution ({{ q.statistics.totalAnswers }} answers)
              </p>

              <div v-if="q.statistics.totalAnswers === 0" class="text-xs text-cool-gray">
                No answers yet.
              </div>

              <div v-for="(stat, idx) in q.statistics.answers" :key="idx" class="mb-3 last:mb-0">
                <div class="flex justify-between text-sm font-medium text-blue-black mb-1">
                  <span>{{ formatStatisticAnswer(stat) }}</span>
                  <span>{{ stat.percentage.toFixed(1) }}%</span>
                </div>

                <div class="w-full h-2 bg-white rounded-full overflow-hidden border border-charcoal/10">
                  <div class="h-full bg-vibrant-coral rounded-full transition-all duration-500"
                    :style="{ width: stat.percentage + '%' }"></div>
                </div>

                <p class="text-xs text-cool-gray mt-1">
                  {{ stat.count }} players
                </p>
              </div>
            </div>
          </div>
        </template>
      </section>

      <!-- Footer -->
      <footer class="flex items-center justify-end gap-3 border-t border-charcoal/10 px-5 py-4 bg-white rounded-b-2xl">
        <button
          class="rounded-lg border border-charcoal/20 px-4 py-2 font-alexandria font-semibold text-blue-black hover:bg-dark-white transition"
          @click="$emit('close')">
          Close
        </button>

        <!-- Hide submit in read-only mode -->
        <button v-if="!readOnly"
          class="rounded-lg bg-vibrant-coral px-4 py-2 font-alexandria font-semibold text-white hover:bg-vibrant-coral/90 disabled:opacity-60 shadow-sm transition"
          :disabled="submitting || !challenge" @click="handleSubmit">
          {{ submitting ? 'Submitting…' : 'Submit answers' }}
        </button>
      </footer>
    </div>

    <!-- Fullscreen image modal -->
    <div v-if="zoomedImage" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80"
      @click="closeImage">
      <img :src="zoomedImage" alt="Zoomed image" class="max-h-[90vh] max-w-[90vw] rounded-lg shadow-lg cursor-zoom-out"
        @click.stop />
      <button class="absolute top-4 right-4 text-white text-2xl font-bold hover:text-golden-yellow" @click="closeImage">
        ✕
      </button>
    </div>
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
      isExpired: !!data.isExpired,
      isAnswered: !!data.isAnswered,
      isEvaluated: !!data.isEvaluated,
      isStarted: !!data.isStarted,
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
</script>