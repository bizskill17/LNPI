import { useEffect, useMemo, useState } from "react";
import Spinner from "../components/common/Spinner";
import Pagination from "../components/common/Pagination";
import { apiGet } from "../lib/apiClient";

type ItemRow = {
  itemId: string;
  itemGroup: string;
  itemName: string;
  erp: string | null;
};

type ItemsResponse = { rows: ItemRow[]; total: number };

export default function ItemsList() {
  const [q, setQ] = useState("");
  const [page, setPage] = useState(1);
  const pageSize = 25;

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [data, setData] = useState<ItemsResponse | null>(null);

  const query = useMemo(() => ({ q, page, pageSize }), [q, page]);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      setLoading(true);
      setError(null);
      const res = await apiGet<ItemsResponse>(
        `/items?q=${encodeURIComponent(query.q)}&page=${query.page}&pageSize=${query.pageSize}`
      );
      if (cancelled) return;
      if (!res.ok) {
        setError(res.error);
        setData(null);
      } else {
        setData(res.data);
      }
      setLoading(false);
    })();
    return () => {
      cancelled = true;
    };
  }, [query]);

  const rows = data?.rows ?? [];
  const total = data?.total ?? 0;

  return (
    <div className="card">
      <div className="cardHeader">
        <div className="row" style={{ justifyContent: "space-between" }}>
          <div>
            <div style={{ fontWeight: 700 }}>Items</div>
            <div className="muted" style={{ fontSize: 12 }}>
              Master data
            </div>
          </div>
          <div className="row">
            <input
              className="input"
              value={q}
              placeholder="Search item name / id / group…"
              onChange={(e) => {
                setPage(1);
                setQ(e.target.value);
              }}
            />
          </div>
        </div>
      </div>
      <div className="cardBody">
        {loading ? <Spinner label="Loading items…" /> : null}
        {error ? <div className="pill" style={{ borderColor: "rgba(255,93,93,0.35)", background: "rgba(255,93,93,0.12)" }}>{error}</div> : null}
        {!loading && !error && rows.length === 0 ? (
          <div className="muted">No results</div>
        ) : null}
        {rows.length ? (
          <div style={{ overflow: "auto" }}>
            <table className="table">
              <thead>
                <tr>
                  <th>Item Id</th>
                  <th>Item Name</th>
                  <th>Item Group</th>
                  <th>ERP</th>
                </tr>
              </thead>
              <tbody>
                {rows.map((r) => (
                  <tr key={r.itemId}>
                    <td>{r.itemId}</td>
                    <td>{r.itemName}</td>
                    <td>{r.itemGroup}</td>
                    <td className="muted">{r.erp ?? "-"}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : null}
        <div style={{ marginTop: 12 }}>
          <Pagination page={page} pageSize={pageSize} total={total} onChange={setPage} />
        </div>
      </div>
    </div>
  );
}

