import { Navigate, createBrowserRouter } from "react-router-dom";
import { AppLayout } from "../layouts/AppLayout";
import { GuestRoute, PrivateRoute } from "../components/layout/ProtectedRoute";
import NotFound from "../components/templates/NotFound";
import { AppShell } from "./AppShell";
import {
  DashboardPage,
  LogPage,
  LoginPage,
  MenuPage,
  ParameterPage,
  RolePage,
  UserPage,
} from "./lazyPages";
import { withSuspense } from "./withSuspense";

export const appRouter = createBrowserRouter([
  {
    element: <AppShell />,
    children: [
      {
        element: <GuestRoute />,
        children: [{ path: "/login", element: withSuspense(<LoginPage />) }],
      },
      {
        element: <PrivateRoute />,
        children: [
          {
            path: "/",
            element: <AppLayout />,
            children: [
              { index: true, element: <Navigate to="/dashboard" replace /> },
              { path: "dashboard", element: withSuspense(<DashboardPage />) },
              { path: "system/menu", element: withSuspense(<MenuPage />) },
              { path: "system/role", element: withSuspense(<RolePage />) },
              { path: "system/user", element: withSuspense(<UserPage />) },
              { path: "system/parameter", element: withSuspense(<ParameterPage />) },
              { path: "system/log", element: withSuspense(<LogPage />) },
            ],
          },
        ],
      },
      { path: "*", element: <NotFound /> },
    ],
  },
]);
