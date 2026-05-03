export default function Dashboard() {
  return (
    <div className="card">
      <div className="cardHeader">
        <div style={{ fontWeight: 700 }}>Welcome</div>
        <div className="muted" style={{ fontSize: 12 }}>
          React frontend + PHP/MySQL backend (shared hosting friendly)
        </div>
      </div>
      <div className="cardBody">
        <div className="row">
          <span className="pill">Next</span>
          <span className="muted">Connect API, then build list screens with search + pagination.</span>
        </div>
      </div>
    </div>
  );
}

