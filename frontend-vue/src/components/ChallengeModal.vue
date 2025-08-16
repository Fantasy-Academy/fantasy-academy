<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="w-full max-w-2xl rounded-lg bg-white shadow-lg">
      <header class="flex items-center justify-between border-b px-5 py-4">
        <h2 class="text-xl font-semibold">{{ challenge?.name || 'Challenge' }}</h2>
        <button class="text-gray-500 hover:text-black" @click="$emit('close')">✕</button>
      </header>

      <section class="max-h-[70vh] overflow-y-auto px-5 py-4">
        <!-- Stavy -->
        <div v-if="loading" class="text-gray-600">Načítám challenge…</div>
        <div v-else-if="error" class="text-red-600">{{ error }}</div>

        <template v-else-if="challenge">
          <!-- hlavní obrázek challenge (volitelné) -->
          <img
            v-if="challengeImgSrc"
            :src="challengeImgSrc"
            :alt="challenge.name"
            class="mb-3 max-h-48 w-full rounded object-cover"
            @error="onImgError"
          />

          <p class="mb-2 text-gray-700">{{ challenge.description }}</p>

          <!-- hint -->
          <div v-if="challenge.hintText || hintImgSrc" class="mb-4 rounded border bg-blue-50 p-3 text-sm">
            <p v-if="challenge.hintText" class="mb-2">
              <strong>Hint:</strong> {{ challenge.hintText }}
            </p>
            <img
              v-if="hintImgSrc"
              :src="hintImgSrc"
              alt="Hint"
              class="max-h-48 w-full rounded object-contain"
              @error="onImgError"
            />
          </div>

          <!-- Otázky -->
          <div v-for="q in questions" :key="q.id" class="mb-5">
            <!-- single_select -->
            <QuestionSingleSelect
              v-if="q.type === 'single_select'"
              :question="toSingleSelectUI(q)"
              v-model="answers[q.id]"
            />

            <!-- multi_select -->
            <QuestionMultiSelect
              v-else-if="q.type === 'multi_select'"
              :question="toMultiSelectUI(q)"
              v-model="answers[q.id]"
            />

            <!-- text -->
            <QuestionText
              v-else-if="q.type === 'text'"
              :question="{ id: q.id, text: q.text, hint: null }"
              v-model="answers[q.id]"
            />

            <!-- numeric -->
            <QuestionNumeric
              v-else-if="q.type === 'numeric'"
              :question="toNumericUI(q)"
              v-model="answers[q.id]"
            />

            <!-- sort -->
            <QuestionSort
              v-else-if="q.type === 'sort'"
              :question="toSortUI(q)"
              v-model="sortModels[q.id]"
            />

            <div v-else class="text-sm text-gray-500">Neznámý typ otázky: {{ q.type }}</div>
          </div>
        </template>
      </section>

      <footer class="flex items-center justify-end gap-3 border-t px-5 py-4">
        <button class="rounded-lg border px-4 py-2 hover:bg-gray-50" @click="$emit('close')">
          Zavřít
        </button>
        <button
          class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700 disabled:opacity-60"
          :disabled="submitting || !challenge"
          @click="handleSubmit"
        >
          {{ submitting ? 'Odesílám…' : 'Odeslat odpovědi' }}
        </button>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, computed } from 'vue';
import { apiGetChallengeDetail, apiAnswerChallenge } from '@/api/challenges';
import { resolvedImage, onImgError } from '@/utils/imageHelpers';

// Vaše existující komponenty pro jednotlivé typy:
import QuestionSingleSelect from '../components/questions/SingleSelectQuestion.vue';
import QuestionMultiSelect   from '../components/questions/MultiSelectQuestion.vue';
import QuestionNumeric       from '../components/questions/NumericQuestion.vue';
import QuestionText          from '../components/questions/TextQuestion.vue';
import QuestionSort          from '../components/questions/SortQuestion.vue';

const props = defineProps({
  show: { type: Boolean, default: false },
  challengeId: { type: String, required: false },
});
const emit = defineEmits(['close', 'submitted']);

const loading    = ref(false);
const error      = ref(null);
const challenge  = ref(null);
const questions  = ref([]);       // otázky v API tvaru
const answers    = reactive({});  // map questionId -> model (viz níže)
const sortModels = reactive({});  // map questionId -> array of choice objects (UI), slouží jen pro zobrazení

const submitting = ref(false);

const log = (...a) => console.log('[ChallengeModal]', ...a);
const err = (...a) => console.error('[ChallengeModal]', ...a);

// absolutní URL pro hlavní obrázek a hint image
const challengeImgSrc = computed(() => resolvedImage(challenge.value));
const hintImgSrc = computed(() =>
  challenge.value?.hintImage ? resolvedImage({ image: challenge.value.hintImage }) : null
);

/** Pomocné převody pro UI komponenty **/
function toSingleSelectUI(q) {
  return {
    id: q.id,
    text: q.text,
    hint: null,
    options: (q.choiceConstraint?.choices || []).map(c => ({
      id: c.id,
      label: c.text,
      value: c.id, // v modelu držíme přímo ID volby
      description: c.description ?? null,
      image: c.image ? resolvedImage({ image: c.image }) : null,
    })),
  };
}

function toMultiSelectUI(q) {
  return {
    id: q.id,
    text: q.text,
    hint: null,
    options: (q.choiceConstraint?.choices || []).map(c => ({
      id: c.id,
      label: c.text,
      value: c.id, // v modelu držíme přímo ID volby
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
  // UI komponenta zatím jen zobrazuje, pořadí držíme v sortModels[q.id] jako pole objektů
  const opts = (q.choiceConstraint?.choices || []).map(c => ({
    id: c.id,
    label: c.text,
    image: c.image ? resolvedImage({ image: c.image }) : null,
  }));
  return {
    id: q.id,
    text: q.text,
    hint: 'Pořadí odpovědí – drag & drop doplníme později.',
    options: opts,
  };
}

function rangeHint(constraint) {
  if (!constraint) return null;
  const hasMin = typeof constraint.min === 'number';
  const hasMax = typeof constraint.max === 'number';
  if (hasMin && hasMax) return `Zadej číslo v rozsahu ${constraint.min}–${constraint.max}.`;
  if (hasMin) return `Zadej číslo ≥ ${constraint.min}.`;
  if (hasMax) return `Zadej číslo ≤ ${constraint.max}.`;
  return null;
}

/** Inicializace modelů odpovědí podle typu **/
function initAnswerModels(apiQuestions) {
  apiQuestions.forEach(q => {
    switch (q.type) {
      case 'single_select': {
        const preset = q.answer?.selectedChoiceId || null;
        answers[q.id] = preset;
        break;
      }
      case 'multi_select': {
        const preset = Array.isArray(q.answer?.selectedChoiceIds) ? q.answer.selectedChoiceIds : [];
        answers[q.id] = preset;
        break;
      }
      case 'text': {
        answers[q.id] = q.answer?.textAnswer ?? '';
        break;
      }
      case 'numeric': {
        const num = typeof q.answer?.numericAnswer === 'number' ? q.answer.numericAnswer : null;
        answers[q.id] = num;
        break;
      }
      case 'sort': {
        // Model pro odevzdání: pole ID v pořadí
        const base = (q.choiceConstraint?.choices || []).map(c => c.id);
        const preset = Array.isArray(q.answer?.orderedChoiceIds) ? q.answer.orderedChoiceIds : base;
        answers[q.id] = preset.slice(); // pole stringů (id)

        // UI pro sort komponentu: potřebuje pole objektů { id,label } v pořadí
        const opts = (q.choiceConstraint?.choices || []).map(c => ({ id: c.id, label: c.text }));
        const ordered = preset.map(id => opts.find(o => o.id === id)).filter(Boolean);
        sortModels[q.id] = ordered;
        break;
      }
      default:
        answers[q.id] = null;
    }
  });
}

/** Když se vizuální pořadí (sortModels) změní, přepiš odpověď do answers */
watch(
  () => ({ ...sortModels }),
  () => {
    for (const qId of Object.keys(sortModels)) {
      const arr = sortModels[qId];
      if (Array.isArray(arr)) {
        answers[qId] = arr.map(o => o.id);
      }
    }
  },
  { deep: true }
);

/** Načtení detailu */
async function fetchChallenge() {
  if (!props.challengeId) {
    log('⚠️ fetchChallenge skipped: žádné challengeId');
    return;
  }

  loading.value = true;
  error.value = null;

  try {
    const data = await apiGetChallengeDetail(props.challengeId);
    challenge.value = {
      id: data.id,
      name: data.name,
      description: data.description || '',
      hintText: data.hintText ?? null,
      hintImage: data.hintImage ?? null,
    };
    questions.value = Array.isArray(data.questions) ? data.questions : [];
    initAnswerModels(questions.value);
  } catch (e) {
    err('fetchChallenge failed:', e);
    error.value = e?.message || 'Nepodařilo se načíst challenge';
  } finally {
    loading.value = false;
  }
}

/** Submit odpovědí dle schématu Answer.challenge */
async function handleSubmit() {
  if (!challenge.value) return;

  const payload = {
    challengeId: challenge.value.id,
    answers: questions.value.map(q => {
    const model = answers[q.id];
    // Překlad do schématu Answer
    const answer = {
      textAnswer:        null,
      numericAnswer:     null,
      selectedChoiceId:  null,
      selectedChoiceIds: null,
      orderedChoiceIds:  null,
    };

    switch (q.type) {
      case 'single_select':
        answer.selectedChoiceId = model || null;            // string | null
        break;
      case 'multi_select':
        answer.selectedChoiceIds = Array.isArray(model) ? model : []; // string[]
        break;
      case 'text':
        answer.textAnswer = (model ?? '').toString();
        break;
      case 'numeric':
        answer.numericAnswer = (model === '' || model === null) ? null : Number(model);
        break;
      case 'sort':
        answer.orderedChoiceIds = Array.isArray(model) ? model : [];
        break;
    }

    return { questionId: q.id, answer };
    }),
  };

  submitting.value = true;
  try {
    await apiAnswerChallenge(payload);
    emit('submitted');
  } catch (e) {
    err('handleSubmit failed:', e);
    alert(e?.message || 'Chyba při odeslání odpovědí.');
  } finally {
    submitting.value = false;
  }
}

// mount & watchers
onMounted(() => { if (props.show) fetchChallenge(); });
watch(() => props.show, (val) => { if (val) fetchChallenge(); });
watch(() => props.challengeId, (val, oldVal) => { if (props.show && val && val !== oldVal) fetchChallenge(); });
</script>