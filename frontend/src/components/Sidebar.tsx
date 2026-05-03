export type RouteKey =
  | "dashboard"
  | "items"
  | "itemGroups"
  | "uoms"
  | "materialIn"
  | "consumption"
  | "production";

export default function Sidebar({
  active,
  onNavigate
}: {
  active: RouteKey;
  onNavigate: (key: RouteKey) => void;
}) {
  const sections: Array<{ title: string; items: Array<{ key: RouteKey; label: string }> }> = [
    { title: "Overview", items: [{ key: "dashboard", label: "Dashboard" }] },
    {
      title: "Masters",
      items: [
        { key: "items", label: "Items" },
        { key: "itemGroups", label: "Item Groups" },
        { key: "uoms", label: "UOM" }
      ]
    },
    {
      title: "Operations",
      items: [
        { key: "materialIn", label: "Material In" },
        { key: "consumption", label: "Consumption" },
        { key: "production", label: "Production" }
      ]
    }
  ];

  return (
    <aside className="sidebar">
      <div style={{ fontWeight: 800, letterSpacing: 0.4, marginBottom: 14 }}>Item Management</div>
      {sections.map((s) => (
        <div key={s.title} style={{ marginBottom: 14 }}>
          <div className="muted" style={{ fontSize: 12, margin: "10px 0" }}>
            {s.title}
          </div>
          <div style={{ display: "flex", flexDirection: "column", gap: 8 }}>
            {s.items.map((i) => (
              <button
                key={i.key}
                className="btn"
                onClick={() => onNavigate(i.key)}
                style={{
                  textAlign: "left",
                  background: active === i.key ? "rgba(102, 227, 255, 0.14)" : undefined,
                  borderColor: active === i.key ? "rgba(102, 227, 255, 0.28)" : undefined
                }}
              >
                {i.label}
              </button>
            ))}
          </div>
        </div>
      ))}
    </aside>
  );
}
