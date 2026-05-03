export default function Pagination({
  page,
  pageSize,
  total,
  onChange
}: {
  page: number;
  pageSize: number;
  total: number;
  onChange: (nextPage: number) => void;
}) {
  const totalPages = Math.max(1, Math.ceil(total / pageSize));
  const canPrev = page > 1;
  const canNext = page < totalPages;

  return (
    <div className="row" style={{ justifyContent: "space-between" }}>
      <span className="muted">
        Page {page} / {totalPages} • {total} rows
      </span>
      <div className="row">
        <button className="btn" disabled={!canPrev} onClick={() => onChange(page - 1)}>
          Prev
        </button>
        <button className="btn" disabled={!canNext} onClick={() => onChange(page + 1)}>
          Next
        </button>
      </div>
    </div>
  );
}

