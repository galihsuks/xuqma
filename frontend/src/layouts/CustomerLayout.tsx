import { MonitorSmartphone, Package, ReceiptText, ShoppingCart, UserRound } from "lucide-react";
import { Link, Outlet, useLocation, useNavigate } from "react-router-dom";
import { AppLogo } from "../components/shared/AppLogo";
import { Button } from "../components/ui";
import { usePageTitle } from "../hooks/usePageTitle";
import { queryClient } from "../lib/queryClient";
import { useAuthLogoutMutation } from "../api/auth/authQuery";
import { useAuthActions, useUser } from "../store/authStore";
import { useNotificationStore } from "../store/notifStore";
import { cn } from "../utils/cn";

const customerNavItems = [
  { label: "Cart", path: "/customer/cart", icon: ShoppingCart },
  { label: "Orders", path: "/customer/orders", icon: Package },
  { label: "History", path: "/customer/history", icon: ReceiptText },
  { label: "Profile", path: "/customer/profile", icon: UserRound },
];

export const CustomerLayout = () => {
  usePageTitle("Customer Workspace");

  const location = useLocation();
  const navigate = useNavigate();
  const user = useUser();
  const { logout } = useAuthActions();
  const addToast = useNotificationStore((state) => state.addToast);
  const { mutate: logoutMutation, isPending: isLogoutPending } = useAuthLogoutMutation();

  const onLogout = () => {
    logoutMutation(undefined, {
      onSuccess: (response) => {
        if (response.message) {
          addToast(response.message, "success");
        }
      },
      onError: (error) => {
        addToast(error.message, "error");
      },
      onSettled: () => {
        queryClient.clear();
        logout();
        navigate("/login");
      },
    });
  };

  return (
    <main className="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(244,114,182,0.18),transparent_28%),radial-gradient(circle_at_top_right,rgba(167,139,250,0.22),transparent_24%),linear-gradient(180deg,var(--color-light-100),white)]">
      <div className="mx-auto max-w-7xl px-4 py-6 md:px-6 md:py-8">
        <header className="rounded-[28px] border border-primary-100 bg-white/92 p-4 shadow-[0_24px_70px_-45px_rgba(236,72,153,0.6)] backdrop-blur">
          <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div className="flex items-center gap-3">
              <div className="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-secondary-500 text-white shadow-lg shadow-primary-200/70">
                <MonitorSmartphone className="h-5 w-5" />
              </div>
              <div>
                <p className="text-xs font-semibold uppercase tracking-[0.22em] text-primary-700">
                  IT Commerce
                </p>
                <AppLogo variant="text" className="h-7 w-auto" />
              </div>
            </div>

            <div className="flex flex-wrap items-center gap-2">
              {customerNavItems.map((item) => {
                const Icon = item.icon;
                const active =
                  location.pathname === item.path || location.pathname.startsWith(`${item.path}/`);

                return (
                  <Link
                    key={item.path}
                    to={item.path}
                    className={cn(
                      "inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition",
                      active
                        ? "bg-primary-600 text-white shadow-lg shadow-primary-200/80"
                        : "bg-primary-50 text-primary-700 hover:bg-primary-100",
                    )}
                  >
                    <Icon className="h-4 w-4" />
                    {item.label}
                  </Link>
                );
              })}
            </div>
          </div>

          <div className="mt-4 flex flex-col gap-3 border-t border-primary-100 pt-4 md:flex-row md:items-center md:justify-between">
            <div>
              <p className="text-sm text-dark-500">Signed in as</p>
              <p className="text-sm font-semibold text-dark-900">{user?.full_name ?? "Customer"}</p>
            </div>
            <div className="flex flex-wrap gap-2">
              <Button type="link" link="/admin/dashboard" variant="secondary-outline">
                Open Admin
              </Button>
              <Button variant="dark-outline" loading={isLogoutPending} onClick={onLogout}>
                {isLogoutPending ? "Signing out..." : "Sign out"}
              </Button>
            </div>
          </div>
        </header>

        <section className="mt-6 rounded-[30px] border border-primary-100 bg-white p-6 shadow-[0_24px_70px_-50px_rgba(167,139,250,0.5)] md:p-8">
          <Outlet />
        </section>
      </div>
    </main>
  );
};
