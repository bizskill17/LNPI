import { useMemo, useState } from "react";
import Sidebar, { RouteKey } from "./components/Sidebar";
import TopBar from "./components/TopBar";
import Dashboard from "./views/Dashboard";
import ItemsList from "./views/ItemsList";
import PlaceholderList from "./views/PlaceholderList";
import SimpleMasterList from "./views/SimpleMasterList";

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
      case "itemGroups":
        return { title: "Item Groups", subtitle: "Masters" };
      case "uoms":
        return { title: "UOM", subtitle: "Masters" };
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
          {view === "itemGroups" ? (
            <SimpleMasterList
              title="Item Groups"
              subtitle="Create / edit / delete item groups"
              listPath="/item-groups/"
              createPath="/item-groups/"
              updatePath={(id) => `/item-groups/item/?id=${id}`}
              deletePath={(id) => `/item-groups/item/?id=${id}`}
              fieldKey="itemGroup"
              fieldLabel="Item Group"
            />
          ) : null}
          {view === "uoms" ? (
            <SimpleMasterList
              title="UOM"
              subtitle="Create / edit / delete units"
              listPath="/uoms/"
              createPath="/uoms/"
              updatePath={(id) => `/uoms/item/?id=${id}`}
              deletePath={(id) => `/uoms/item/?id=${id}`}
              fieldKey="uom"
              fieldLabel="UOM"
            />
          ) : null}
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
