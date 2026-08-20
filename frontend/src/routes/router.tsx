import { Navigate, createBrowserRouter } from "react-router-dom";
import { AppLayout } from "../layouts/AppLayout";
import { CustomerLayout } from "../layouts/CustomerLayout";
import { GuestRoute, PrivateRoute } from "../components/layout/ProtectedRoute";
import NotFound from "../components/templates/NotFound";
import { AppShell } from "./AppShell";
import {
  ArticlePage,
  CategoryPage,
  CustomerCartPage,
  CustomerOrderDetailPage,
  CustomerHistoryPage,
  CustomerOrdersPage,
  CustomerProfilePage,
  DashboardPage,
  LogPage,
  MenuPage,
  OrderPage,
  OrderDetailPage,
  ParameterPage,
  ProductPage,
  RolePage,
  UserPage,
} from "./lazyPages";
import { withSuspense } from "./withSuspense";

export const appRouter = createBrowserRouter([
  {
    element: <AppShell />,
    children: [
      {
        path: "/login",
        element: <GuestRoute />,
      },
      {
        element: <PrivateRoute />,
        children: [
          {
            path: "/customer",
            element: <CustomerLayout />,
            children: [
              { index: true, element: <Navigate to="/customer/cart" replace /> },
              { path: "cart", element: withSuspense(<CustomerCartPage />) },
              { path: "orders", element: withSuspense(<CustomerOrdersPage />) },
              { path: "orders/:id", element: withSuspense(<CustomerOrderDetailPage />) },
              { path: "history/:id", element: withSuspense(<CustomerOrderDetailPage />) },
              { path: "history", element: withSuspense(<CustomerHistoryPage />) },
              { path: "profile", element: withSuspense(<CustomerProfilePage />) },
            ],
          },
          {
            path: "/admin",
            element: <AppLayout />,
            children: [
              { index: true, element: <Navigate to="/admin/dashboard" replace /> },
              { path: "dashboard", element: withSuspense(<DashboardPage />) },
              { path: "catalog/categories", element: withSuspense(<CategoryPage />) },
              { path: "catalog/products", element: withSuspense(<ProductPage />) },
              { path: "articles", element: withSuspense(<ArticlePage />) },
              { path: "orders", element: withSuspense(<OrderPage />) },
              { path: "orders/:id", element: withSuspense(<OrderDetailPage />) },
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
