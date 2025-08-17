import { apiFetch } from '@/api/http';

export async function apiGetLeaderboards(page = 1) {
  const data = await apiFetch(`/api/leaderboards?page=${encodeURIComponent(page)}`, { auth: true });

  // Normalize hydra or plain array
  const list = Array.isArray(data) ? data : (Array.isArray(data?.member) ? data.member : []);
  const totalItems = typeof data?.totalItems === 'number' ? data.totalItems : list.length;
  const view = data?.view || null;

  // Attempt to parse last page from hydra.view.last (?page=N)
  let lastPage = 1;
  if (view?.last) {
    const m = view.last.match(/[\?&]page=(\d+)/i);
    lastPage = m ? parseInt(m[1], 10) || 1 : 1;
  }

  return { items: list, totalItems, lastPage };
}