import { lazy } from "react";

export const LoginPage = lazy(() =>
  import("../pages/auth/login/LoginPage").then((module) => ({ default: module.LoginPage })),
);

export const DashboardPage = lazy(() =>
  import("../pages/main/dashboard/DashboardPage").then((module) => ({
    default: module.DashboardPage,
  })),
);

export const CustomerCartPage = lazy(() =>
  import("../pages/customer/cart/CustomerCartPage").then((module) => ({
    default: module.CustomerCartPage,
  })),
);

export const CustomerOrdersPage = lazy(() =>
  import("../pages/customer/orders/CustomerOrdersPage").then((module) => ({
    default: module.CustomerOrdersPage,
  })),
);

export const CustomerOrderDetailPage = lazy(() =>
  import("../pages/customer/orders/CustomerOrderDetailPage").then((module) => ({
    default: module.CustomerOrderDetailPage,
  })),
);

export const CustomerHistoryPage = lazy(() =>
  import("../pages/customer/history/CustomerHistoryPage").then((module) => ({
    default: module.CustomerHistoryPage,
  })),
);

export const CustomerProfilePage = lazy(() =>
  import("../pages/customer/profile/CustomerProfilePage").then((module) => ({
    default: module.CustomerProfilePage,
  })),
);

export const ProductPage = lazy(() =>
  import("../pages/admin/product/ProductPage").then((module) => ({
    default: module.ProductPage,
  })),
);

export const CategoryPage = lazy(() =>
  import("../pages/admin/category/CategoryPage").then((module) => ({
    default: module.CategoryPage,
  })),
);

export const ArticlePage = lazy(() =>
  import("../pages/admin/article/ArticlePage").then((module) => ({
    default: module.ArticlePage,
  })),
);

export const OrderPage = lazy(() =>
  import("../pages/admin/order/OrderPage").then((module) => ({
    default: module.OrderPage,
  })),
);

export const OrderDetailPage = lazy(() =>
  import("../pages/admin/order/OrderDetailPage").then((module) => ({
    default: module.OrderDetailPage,
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
