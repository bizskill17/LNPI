import { useMemo, useState } from "react";
import Sidebar, { RouteKey } from "./components/Sidebar";
import TopBar from "./components/TopBar";
import Dashboard from "./views/Dashboard";
import ItemsList from "./views/ItemsList";
import PlaceholderList from "./views/PlaceholderList";

export default function App() {
  const [view, setView] = useState<RouteKey>("dashboard");

  const header = useMemo(() => {
    switch (view) {
      case "dashboard":
        return { title: "Dashboard", subtitle: "At-a-glance" };
      case "items":
        return { title: "Items", subtitle: "Masters" };
      case "materialIn":
        return { title: "Material In", subtitle: "Purchases / inward" };
      case "consumption":
        return { title: "Consumption", subtitle: "Issues" };
      case "production":
        return { title: "Production", subtitle: "Outputs" };
      default:
        return { title: "LNPI", subtitle: "" };
    }
  }, [view]);

  return (
    <div className="appShell">
      <Sidebar active={view} onNavigate={setView} />
      <div className="content">
        <TopBar title={header.title} subtitle={header.subtitle} />
        <main className="page">
          {view === "dashboard" ? <Dashboard /> : null}
          {view === "items" ? <ItemsList /> : null}
          {view === "materialIn" ? (
            <PlaceholderList title="Material In" note="Header + line items (Material In Items)" />
          ) : null}
          {view === "consumption" ? <PlaceholderList title="Consumption" note="Transactions" /> : null}
          {view === "production" ? <PlaceholderList title="Production" note="Transactions" /> : null}
        </main>
      </div>
    </div>
  );
}

