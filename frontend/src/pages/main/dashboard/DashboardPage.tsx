import { Button } from "../../../components/ui";
import { PageHeader } from "../../../components/layout/PageHeader";
import { usePageTitle } from "../../../hooks/usePageTitle";
import { useUser } from "../../../store/authStore";
import { useNotificationStore } from "../../../store/notifStore";

export const DashboardPage = () => {
  usePageTitle("Dashboard");

  const user = useUser();
  const addToast = useNotificationStore((state) => state.addToast);

  return (
    <>
      <PageHeader
        title={`Welcome back, ${user?.full_name ?? "User"}`}
        subtitle="Your base app is ready. Continue building modules and workflows based on your project needs."
        breadcrumbs={[
          { label: "Main", route: undefined },
          { label: "Dashboard", route: undefined },
        ]}
      />

      <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 className="text-lg font-semibold text-slate-800">Dashboard</h2>
        <p className="mt-1 text-sm text-slate-500">
          Base app is ready to use. Continue integrating modules based on your needs.
        </p>
        <Button
          type="button"
          variant="primary"
          className="mt-3"
          onClick={() => addToast("Example info notification", "info")}
        >
          Test Toast
        </Button>
        <div className="mt-2 flex gap-1 flex-wrap">
          <Button type="button" variant="primary">
            Primary
          </Button>
          <Button type="button" variant="primary-outline">
            Primary Outline
          </Button>
        </div>
        <div className="mt-2 flex gap-1 flex-wrap">
          <Button type="button" variant="secondary">
            secondary
          </Button>
          <Button type="button" variant="secondary-outline">
            secondary Outline
          </Button>
        </div>
        <div className="mt-2 flex gap-1 flex-wrap">
          <Button type="button" variant="dark">
            dark
          </Button>
          <Button type="button" variant="dark-outline">
            dark Outline
          </Button>
        </div>
        <div className="mt-2 flex gap-1 flex-wrap">
          <Button type="button" variant="light">
            light
          </Button>
          <Button type="button" variant="light-outline">
            light Outline
          </Button>
        </div>
        <div className="mt-2 flex gap-1 flex-wrap">
          <Button type="button" variant="danger">
            danger
          </Button>
          <Button type="button" variant="danger-outline">
            danger Outline
          </Button>
        </div>
        <div className="mt-2 flex gap-1 flex-wrap">
          <Button type="button" variant="success">
            success
          </Button>
          <Button type="button" variant="success-outline">
            success Outline
          </Button>
        </div>
        <div className="mt-2 flex gap-1 flex-wrap">
          <Button type="button" variant="info">
            info
          </Button>
          <Button type="button" variant="info-outline">
            info Outline
          </Button>
        </div>
        <div className="mt-2 flex gap-1 flex-wrap">
          <Button type="button" variant="warning">
            warning
          </Button>
          <Button type="button" variant="warning-outline">
            warning Outline
          </Button>
        </div>
      </section>
    </>
  );
};
