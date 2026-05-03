import { useEffect, useState } from "react";
import Spinner from "../components/common/Spinner";
import { apiGet, apiSend } from "../lib/apiClient";

export default function SimpleMasterList({
  title,
  subtitle,
  listPath,
  createPath,
  updatePath,
  deletePath,
  fieldKey,
  fieldLabel
}: {
  title: string;
  subtitle: string;
  listPath: string; // e.g. "/uoms/"
  createPath: string; // e.g. "/uoms/"
  updatePath: (id: number) => string; // e.g. (id)=>`/uoms/item/?id=${id}`
  deletePath: (id: number) => string;
  fieldKey: string; // e.g. "uom"
  fieldLabel: string; // e.g. "UOM"
}) {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [rows, setRows] = useState<Array<{ id: number; value: string }>>([]);
  const [newValue, setNewValue] = useState("");

  async function load() {
    setLoading(true);
    setError(null);
    const res = await apiGet<{ rows: Array<any> }>(listPath);
    if (!res.ok) {
      setError(res.error);
      setRows([]);
    } else {
      setRows(
        res.data.rows.map((r: any) => ({
          id: Number(r.id),
          value: String(r[fieldKey])
        }))
      );
    }
    setLoading(false);
  }

  useEffect(() => {
    load();
  }, []);

  async function create() {
    const value = newValue.trim();
    if (!value) return;
    setError(null);
    const res = await apiSend<any>(createPath, { method: "POST", body: { [fieldKey]: value } });
    if (!res.ok) setError(res.error);
    setNewValue("");
    await load();
  }

  async function edit(id: number, current: string) {
    const next = prompt(`Edit ${fieldLabel}`, current);
    if (next === null) return;
    const value = next.trim();
    if (!value) return;
    setError(null);
    const res = await apiSend<any>(updatePath(id), { method: "PUT", body: { [fieldKey]: value } });
    if (!res.ok) setError(res.error);
    await load();
  }

  async function del(id: number, current: string) {
    if (!confirm(`Delete ${fieldLabel} "${current}"?`)) return;
    setError(null);
    const res = await apiSend<any>(deletePath(id), { method: "DELETE" });
    if (!res.ok) setError(res.error);
    await load();
  }

  return (
    <div className="card">
      <div className="cardHeader">
        <div style={{ fontWeight: 700 }}>{title}</div>
        <div className="muted" style={{ fontSize: 12 }}>
          {subtitle}
        </div>
      </div>
      <div className="cardBody">
        {loading ? <Spinner label={`Loading ${title.toLowerCase()}…`} /> : null}
        {error ? (
          <div
            className="pill"
            style={{ borderColor: "rgba(255,93,93,0.35)", background: "rgba(255,93,93,0.12)" }}
          >
            {error}
          </div>
        ) : null}

        <div className="row" style={{ marginTop: 10 }}>
          <input
            className="input"
            placeholder={`New ${fieldLabel}…`}
            value={newValue}
            onChange={(e) => setNewValue(e.target.value)}
          />
          <button className="btn btnPrimary" onClick={create}>
            Add
          </button>
        </div>

        <div style={{ marginTop: 12, overflow: "auto" }}>
          <table className="table">
            <thead>
              <tr>
                <th>{fieldLabel}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {rows.map((r) => (
                <tr key={r.id}>
                  <td>{r.value}</td>
                  <td>
                    <div className="row" style={{ justifyContent: "flex-end" }}>
                      <button className="btn" onClick={() => edit(r.id, r.value)}>
                        Edit
                      </button>
                      <button className="btn" onClick={() => del(r.id, r.value)}>
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
              {rows.length === 0 && !loading ? (
                <tr>
                  <td className="muted" colSpan={2}>
                    No rows yet
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}

