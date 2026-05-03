import { useEffect, useMemo, useState } from "react";
import Spinner from "../components/common/Spinner";
import Pagination from "../components/common/Pagination";
import { apiGet, apiSend } from "../lib/apiClient";

type ItemRow = {
  id: number;
  itemGroup: string;
  itemGroupId: number;
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
  const [groups, setGroups] = useState<Array<{ id: number; itemGroup: string }>>([]);
  const [modalOpen, setModalOpen] = useState(false);
  const [mode, setMode] = useState<"create" | "edit">("create");
  const [form, setForm] = useState<{ id: number; itemName: string; itemGroupId: number; erp: string }>({
    id: 0,
    itemName: "",
    itemGroupId: 0,
    erp: ""
  });
  const [saving, setSaving] = useState(false);

  const query = useMemo(() => ({ q, page, pageSize }), [q, page]);

  async function loadGroups() {
    const res = await apiGet<{ rows: Array<{ id: number; itemGroup: string }> }>(`/item-groups/`);
    if (res.ok) setGroups(res.data.rows);
  }

  useEffect(() => {
    loadGroups();
  }, []);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      setLoading(true);
      setError(null);
      const res = await apiGet<ItemsResponse>(
        `/items/?q=${encodeURIComponent(query.q)}&page=${query.page}&pageSize=${query.pageSize}`
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

  function openCreate() {
    setMode("create");
    setForm({ id: 0, itemName: "", itemGroupId: groups[0]?.id ?? 0, erp: "" });
    setModalOpen(true);
  }

  function openEdit(r: ItemRow) {
    setMode("edit");
    setForm({ id: r.id, itemName: r.itemName, itemGroupId: r.itemGroupId, erp: r.erp ?? "" });
    setModalOpen(true);
  }

  async function save() {
    setSaving(true);
    setError(null);
    const body = {
      itemName: form.itemName.trim(),
      itemGroupId: form.itemGroupId,
      erp: form.erp.trim()
    };
    const res =
      mode === "create"
        ? await apiSend<{ ok: true }>(`/items/`, { method: "POST", body })
        : await apiSend<{ ok: true }>(`/items/item/?id=${form.id}`, {
            method: "PUT",
            body: { itemName: body.itemName, itemGroupId: body.itemGroupId, erp: body.erp }
          });
    if (!res.ok) setError(res.error);
    if (res.ok) {
      setModalOpen(false);
      await loadGroups();
      setPage(1);
      setQ("");
    }
    setSaving(false);
  }

  async function del(itemId: string) {
    if (!confirm(`Delete this item?`)) return;
    setSaving(true);
    const res = await apiSend<{ ok: true }>(`/items/item/?id=${encodeURIComponent(itemId)}`, { method: "DELETE" });
    if (!res.ok) setError(res.error);
    setSaving(false);
    setPage(1);
    setQ("");
  }

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
            <button className="btn btnPrimary" onClick={openCreate}>
              + Add
            </button>
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
                  <th>Item Name</th>
                  <th>Item Group</th>
                  <th>ERP</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {rows.map((r) => (
                  <tr key={r.id}>
                    <td>{r.itemName}</td>
                    <td>{r.itemGroup}</td>
                    <td className="muted">{r.erp ?? "-"}</td>
                    <td>
                      <div className="row" style={{ justifyContent: "flex-end" }}>
                        <button className="btn" onClick={() => openEdit(r)}>
                          Edit
                        </button>
                        <button className="btn" onClick={() => del(String(r.id))}>
                          Delete
                        </button>
                      </div>
                    </td>
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

      {modalOpen ? (
        <div style={{ marginTop: 12 }} className="card">
          <div className="cardHeader">
            <div style={{ fontWeight: 700 }}>{mode === "create" ? "Add Item" : `Edit Item`}</div>
            <div className="muted" style={{ fontSize: 12 }}>
              {mode === "create" ? "Create a new item" : "Update item details"}
            </div>
          </div>
          <div className="cardBody">
            <div className="row">
              <input
                className="input"
                placeholder="Item Name"
                value={form.itemName}
                onChange={(e) => setForm((f) => ({ ...f, itemName: e.target.value }))}
              />
              <select
                className="input"
                value={String(form.itemGroupId)}
                onChange={(e) => setForm((f) => ({ ...f, itemGroupId: Number(e.target.value) }))}
              >
                {groups.length ? null : <option value="0">(No groups yet)</option>}
                {groups.map((g) => (
                  <option key={g.id} value={String(g.id)}>
                    {g.itemGroup}
                  </option>
                ))}
              </select>
              <input
                className="input"
                placeholder="ERP (optional)"
                value={form.erp}
                onChange={(e) => setForm((f) => ({ ...f, erp: e.target.value }))}
              />
            </div>
            <div className="row" style={{ justifyContent: "flex-end", marginTop: 12 }}>
              <button className="btn" onClick={() => setModalOpen(false)} disabled={saving}>
                Cancel
              </button>
              <button className="btn btnPrimary" onClick={save} disabled={saving}>
                {saving ? "Saving…" : "Save"}
              </button>
            </div>
          </div>
        </div>
      ) : null}
    </div>
  );
}
