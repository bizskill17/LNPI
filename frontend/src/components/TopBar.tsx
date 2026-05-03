export default function TopBar({ title, subtitle }: { title: string; subtitle?: string }) {
  return (
    <div className="topbar">
      <div>
        <div style={{ fontWeight: 700 }}>{title}</div>
        {subtitle ? <div className="muted" style={{ fontSize: 12 }}>{subtitle}</div> : null}
      </div>
      <span className="pill">LNPI</span>
    </div>
  );
}

