import { useEffect, useMemo } from "react";
import { Navigate, Outlet, useLocation } from "react-router-dom";
import { useAccessControlQuery, useAccessMenuQuery } from "../../api/access/accessQuery";
import Forbidden from "../templates/Forbidden";
import InternalServerError from "../templates/InternalServerError";
import { useAuthStore } from "../../store/authStore";
import { useAccessControlActions } from "../../store/accessControlStore";
import { useHttpErrorActions } from "../../store/httpErrorStore";
import { findMenuByPath } from "../../utils/accessControl";

const getRoleHomeRoute = (roleCode?: string | null) => {
  if (roleCode === "C") {
    return "/customer/cart";
  }

  return "/admin/dashboard";
};

export const PrivateRoute = () => {
  const isAuth = useAuthStore((state) => state.isAuthenticated);
  const roleCode = useAuthStore((state) => state.user?.role?.code ?? null);
  const location = useLocation();
  const { clearAccessContext, setAccessContext } = useAccessControlActions();
  const { clearError } = useHttpErrorActions();
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

  if (!isAuth) {
    const redirect = `${location.pathname}${location.search}`;
    return <Navigate to={`/login?redirect=${encodeURIComponent(redirect)}`} replace />;
  }

  if (isCustomerOnly && isAdminRoute) {
    return <Navigate to="/customer/cart" replace />;
  }

  if (isAdminOnly && isCustomerRoute) {
    return <Navigate to="/admin/dashboard" replace />;
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
      !((roleCode === "C" && redirect.startsWith("/admin")) || ((roleCode === "A" || roleCode === "SA") && redirect.startsWith("/customer")))
        ? redirect
        : fallback;
    return <Navigate to={nextRoute} replace />;
  }

  return <Outlet />;
};
