import { Package, ReceiptText, ShoppingCart, UserRound } from "lucide-react";
import { Link, Outlet, useLocation, useNavigate } from "react-router-dom";
import { AppLogo } from "../components/shared/AppLogo";
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
];

const publicNavItems = [
  { label: "Home", path: "/" },
  { label: "Shop", path: "/shop" },
  { label: "Articles", path: "/articles" },
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
      <header className="sticky top-0 z-30 border-b border-white/70 bg-white/80 backdrop-blur-xl">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-5 py-3 lg:px-8">
          <div className="flex-1">
            <nav className="hidden items-center gap-2 md:flex">
              {publicNavItems.map((item, index) => (
                <a
                  key={index}
                  href={item.path}
                  className="rounded-full px-4 py-2 text-sm font-semibold text-dark-600 transition hover:bg-primary-50 hover:text-primary-700"
                >
                  {item.label}
                </a>
              ))}
            </nav>
          </div>
          <a href="/" className="flex items-center gap-1">
            <div className="flex h-7 w-7 items-center justify-center rounded-2xl text-xl font-extrabold text-white shadow-glow">
              <AppLogo variant="icon" className="h-7 w-auto" />
            </div>
            <div>
              <p className="text-lg font-light text-primary-700 tracking-[0.28em]">UQMA</p>
            </div>
          </a>

          <div className="flex-1 flex flex justify-end">
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
                      "rounded-full px-3 py-3 text-sm font-semibold",
                      active
                        ? "text-primary-600"
                        : "text-dark-600 transition hover:bg-primary-50 hover:text-primary-700",
                    )}
                  >
                    <Icon className="h-4 w-4" />
                  </Link>
                );
              })}
              <span className="w-[1px] h-[80%] bg-dark-200"></span>
              <Link
                to={"/customer/profile"}
                className={cn(
                  "rounded-full px-3 py-3 text-sm font-semibold",
                  location.pathname.startsWith("/customer/profile")
                    ? "text-primary-600"
                    : "text-dark-600 transition hover:bg-primary-50 hover:text-primary-700",
                )}
              >
                <UserRound className="h-4 w-4" />
              </Link>
            </div>
          </div>
        </div>
      </header>
      <div className="mx-auto max-w-7xl px-4 py-6 md:px-6 md:py-8">
        <section className="mt-6 rounded-[30px] border border-primary-100 bg-white p-6 shadow-[0_24px_70px_-50px_rgba(167,139,250,0.5)] md:p-8">
          <Outlet />
        </section>
      </div>
    </main>
  );
};
