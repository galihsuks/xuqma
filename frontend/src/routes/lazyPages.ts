import { lazy } from "react";

export const LoginPage = lazy(() =>
  import("../pages/auth/login/LoginPage").then((module) => ({ default: module.LoginPage })),
);

export const DashboardPage = lazy(() =>
  import("../pages/main/dashboard/DashboardPage").then((module) => ({
    default: module.DashboardPage,
  })),
);

export const MenuPage = lazy(() =>
  import("../pages/system/menu/MenuPage").then((module) => ({ default: module.MenuPage })),
);

export const RolePage = lazy(() =>
  import("../pages/system/role/RolePage").then((module) => ({ default: module.RolePage })),
);

export const UserPage = lazy(() =>
  import("../pages/system/user/UserPage").then((module) => ({ default: module.UserPage })),
);

export const ParameterPage = lazy(() =>
  import("../pages/system/parameter/ParameterPage").then((module) => ({
    default: module.ParameterPage,
  })),
);

export const LogPage = lazy(() =>
  import("../pages/system/log/LogPage").then((module) => ({ default: module.LogPage })),
);
