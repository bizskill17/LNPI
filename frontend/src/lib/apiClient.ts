export type ApiResult<T> = { ok: true; data: T } | { ok: false; error: string };

const DEFAULT_BASE = "/api";

export async function apiGet<T>(path: string): Promise<ApiResult<T>> {
  try {
    const res = await fetch(`${DEFAULT_BASE}${path}`, { headers: { Accept: "application/json" } });
    const json = await res.json();
    if (!res.ok) return { ok: false, error: json?.error ?? `HTTP ${res.status}` };
    return { ok: true, data: json as T };
  } catch (e: any) {
    return { ok: false, error: e?.message ?? "Network error" };
  }
}

