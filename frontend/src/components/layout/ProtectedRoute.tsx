import { useEffect, useMemo } from "react";
import { Navigate, Outlet, useLocation } from "react-router-dom";
import { useAccessControlQuery, useAccessMenuQuery } from "../../api/access/accessQuery";
import Forbidden from "../templates/Forbidden";
import InternalServerError from "../templates/InternalServerError";
import { useAuthStore } from "../../store/authStore";
import { useAccessControlActions } from "../../store/accessControlStore";
import { useHttpErrorActions } from "../../store/httpErrorStore";
import { findMenuByPath } from "../../utils/accessControl";

export const PrivateRoute = () => {
  const isAuth = useAuthStore((state) => state.isAuthenticated);
  const location = useLocation();
  const { clearAccessContext, setAccessContext } = useAccessControlActions();
  const { clearError } = useHttpErrorActions();
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
  const location = useLocation();

  if (isAuth) {
    const redirect = new URLSearchParams(location.search).get("redirect");
    const fallback = "/admin/dashboard";
    const nextRoute = redirect && redirect.startsWith("/") ? redirect : fallback;
    return <Navigate to={nextRoute} replace />;
  }

  return <Outlet />;
};
