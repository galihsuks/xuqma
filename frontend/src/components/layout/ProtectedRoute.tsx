import { useEffect, useMemo } from "react";
import { Navigate, Outlet, useLocation } from "react-router-dom";
import { useAccessControlQuery, useAccessMenuQuery } from "../../api/access/accessQuery";
import { useAuthMeQuery } from "../../api/auth/authQuery";
import { useAuthActions, useAuthStore } from "../../store/authStore";
import { useAccessControlActions } from "../../store/accessControlStore";
import { useHttpErrorActions } from "../../store/httpErrorStore";
import Forbidden from "../templates/Forbidden";
import InternalServerError from "../templates/InternalServerError";
import { findMenuByPath } from "../../utils/accessControl";

const getRoleHomeRoute = (roleCode?: string | null) => {
  if (roleCode === "C") {
    return "/customer/cart";
  }

  return "/admin/dashboard";
};

const ExternalLoginRedirect = ({ redirect }: { redirect?: string }) => {
  useEffect(() => {
    const loginUrl = redirect
      ? `/login?redirect=${encodeURIComponent(redirect)}`
      : "/login";

    window.location.replace(loginUrl);
  }, [redirect]);

  return null;
};

export const PrivateRoute = () => {
  const location = useLocation();
  const storedUser = useAuthStore((state) => state.user);
  const isResolved = useAuthStore((state) => state.isResolved);
  const { syncUser } = useAuthActions();
  const { clearAccessContext, setAccessContext } = useAccessControlActions();
  const { clearError } = useHttpErrorActions();
  const { data: meData, isPending: isMePending, error: meError } = useAuthMeQuery();
  const user = meData?.data ?? storedUser;
  const isAuth = Boolean(user);
  const roleCode = user?.role?.code ?? null;
  const isCustomerOnly = roleCode === "C";
  const isAdminOnly = roleCode === "A" || roleCode === "SA";
  const isCustomerRoute = location.pathname.startsWith("/customer");
  const isAdminRoute = location.pathname.startsWith("/admin");
  const {
    data: accessMenuData,
    isPending: isAccessMenuPending,
    error: accessMenuError,
  } = useAccessMenuQuery(isAuth);
  const matchedMenu = useMemo(
    () => findMenuByPath(accessMenuData?.data ?? [], location.pathname),
    [accessMenuData?.data, location.pathname],
  );
  const matchedMenuId = matchedMenu?.id ?? "";
  const {
    data: accessControlData,
    isPending: isAccessControlPending,
    error: accessControlError,
  } = useAccessControlQuery(matchedMenuId, isAuth && Boolean(matchedMenuId));

  useEffect(() => {
    clearError();
  }, [clearError, location.pathname]);

  useEffect(() => {
    if (meData?.data) {
      syncUser(meData.data);
      return;
    }

    if (!isMePending && !meError && isResolved && !storedUser) {
      syncUser(null);
    }
  }, [isMePending, isResolved, meData?.data, meError, storedUser, syncUser]);

  useEffect(() => {
    if (!isAuth || !matchedMenuId) {
      clearAccessContext();
      return;
    }

    setAccessContext(matchedMenuId, accessControlData?.data ?? []);
  }, [accessControlData?.data, clearAccessContext, isAuth, matchedMenuId, setAccessContext]);

  useEffect(() => {
    return () => {
      clearAccessContext();
    };
  }, [clearAccessContext]);

  if (isMePending && !user) {
    return (
      <div className="grid min-h-screen place-items-center bg-slate-100 px-4">
        <div className="rounded-3xl border border-primary-100 bg-white px-6 py-5 text-center shadow-[0_18px_50px_-32px_rgba(236,72,153,0.45)]">
          <p className="text-sm font-semibold uppercase tracking-[0.24em] text-primary-600">
            Authenticating
          </p>
          <p className="mt-2 text-sm text-slate-500">Preparing your workspace...</p>
        </div>
      </div>
    );
  }

  if (!isAuth) {
    const redirect = `${location.pathname}${location.search}`;
    return <ExternalLoginRedirect redirect={redirect} />;
  }

  if (isCustomerOnly && isAdminRoute) {
    return <Navigate to="/customer/cart" replace />;
  }

  if (isAdminOnly && isCustomerRoute) {
    return <Navigate to="/admin/dashboard" replace />;
  }

  if (meError && !user) {
    return null;
  }

  if (accessMenuError || accessControlError) {
    return <InternalServerError />;
  }

  if (
    !isAccessMenuPending &&
    !isAccessControlPending &&
    matchedMenuId &&
    !(accessControlData?.data ?? []).includes("R")
  ) {
    return <Forbidden />;
  }

  return <Outlet />;
};

export const GuestRoute = () => {
  const isAuth = useAuthStore((state) => state.isAuthenticated);
  const roleCode = useAuthStore((state) => state.user?.role?.code ?? null);
  const location = useLocation();

  if (isAuth) {
    const redirect = new URLSearchParams(location.search).get("redirect");
    const fallback = getRoleHomeRoute(roleCode);
    const nextRoute =
      redirect &&
      redirect.startsWith("/") &&
      !(
        (roleCode === "C" && redirect.startsWith("/admin")) ||
        ((roleCode === "A" || roleCode === "SA") && redirect.startsWith("/customer"))
      )
        ? redirect
        : fallback;

    return <Navigate to={nextRoute} replace />;
  }

  const redirect = new URLSearchParams(location.search).get("redirect") ?? undefined;

  return <ExternalLoginRedirect redirect={redirect} />;
};
