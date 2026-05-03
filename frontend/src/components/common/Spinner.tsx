export default function Spinner({ label }: { label?: string }) {
  return (
    <div className="row">
      <span className="pill">Loading</span>
      {label ? <span className="muted">{label}</span> : null}
    </div>
  );
}

