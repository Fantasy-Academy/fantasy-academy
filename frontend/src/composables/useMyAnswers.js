import { ref } from 'vue';
import { apiListChallenges, apiGetChallengeDetail } from '@/api/challenges';
import { toFriendlyError } from '@/utils/errorHandler';
import { normalizeAnswersFromQuestions } from '@/utils/myAnswers';


export function useMyAnswers() {
    const loading = ref(false);
    const error = ref(null);
    const myAnswers = ref([]);
    const totalCount = ref(0);

    async function loadMyAnswers({ page = 1, auth = true, params = {} } = {}) {
        loading.value = true;
        error.value = null;

        try {
            // load challenges without questions
            const { items, total } = await apiListChallenges({ page, auth, params });
            totalCount.value = Number.isFinite(total) ? total : 0;

            const ids = (Array.isArray(items) ? items : []).map(c => c?.id).filter(Boolean);
            if (ids.length === 0) {
                myAnswers.value = [];
                return;
            }

            // get detail of challenges where are questions
            const settled = await Promise.allSettled(ids.map(id => apiGetChallengeDetail(id)));

            // From every challenge detail get answered questions
            const all = [];
            for (const res of settled) {
                if (res.status !== 'fulfilled' || !res.value) {
                    console.warn('[useMyAnswers] detail FAIL', res.reason);
                    continue;
                }
                const ch = res.value;
                const ctx = {
                    challengeId: ch?.id ?? null,
                    challengeName: ch?.name ?? '',
                    challengeAnsweredAt: ch?.answeredAt ?? null,
                };
                const normalized = normalizeAnswersFromQuestions(ch?.questions || [], ctx);
                if (normalized.length) all.push(...normalized);
            }

            // sorted myAnswers by time.
            all.sort((a, b) => (new Date(b.answeredAt || 0)) - (new Date(a.answeredAt || 0)));
            myAnswers.value = all;
        } catch (e) {
            const fe = toFriendlyError(e);
            console.warn('[useMyAnswers] loadMyAnswers FAIL', {
                status: e?.status,
                message: fe.userMessage,
                rawMessage: e?.message,
                data: e?.data,
            });
            error.value = fe.userMessage || 'unable to load myAnswers';
        } finally {
            loading.value = false;
        }
    }
    return { myAnswers, totalCount, loading, error, loadMyAnswers };
}
