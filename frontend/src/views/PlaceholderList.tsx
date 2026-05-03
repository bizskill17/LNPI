export default function PlaceholderList({ title, note }: { title: string; note: string }) {
  return (
    <div className="card">
      <div className="cardHeader">
        <div style={{ fontWeight: 700 }}>{title}</div>
        <div className="muted" style={{ fontSize: 12 }}>
          {note}
        </div>
      </div>
      <div className="cardBody">
        <div className="muted">Scaffolded UI. Next: implement API + list table.</div>
      </div>
    </div>
  );
}

