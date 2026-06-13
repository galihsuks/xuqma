import { Boxes, FileSearch, PackageCheck } from "lucide-react";
import { useMemo } from "react";
import { Badge } from "../../../components/ui";
import { PageHeader } from "../../../components/layout/PageHeader";
import { useArticleListQuery } from "../../../api/article/articleQuery";
import { useOrderListQuery } from "../../../api/order/orderQuery";
import { useProductListQuery } from "../../../api/product/productQuery";
import { usePageTitle } from "../../../hooks/usePageTitle";
import { useUser } from "../../../store/authStore";

export const DashboardPage = () => {
  usePageTitle("Dashboard");

  const user = useUser();
  const { data: productListData } = useProductListQuery({
    page: 1,
    page_size: 100,
  });
  const { data: orderListData } = useOrderListQuery({
    page: 1,
    page_size: 100,
  });
  const { data: articleListData } = useArticleListQuery({
    page: 1,
    page_size: 100,
    status: "published",
  });

  const featuredProducts = useMemo(
    () =>
      (productListData?.data ?? [])
        .filter((product) => product.is_featured === true || product.is_featured === "1")
        .slice(0, 4),
    [productListData?.data],
  );

  const dashboardStats = useMemo(() => {
    const orders = orderListData?.data ?? [];
    const products = productListData?.data ?? [];
    const publishedArticles = articleListData?.pagination?.total_items ?? articleListData?.data?.length ?? 0;
    const lowStockCount = products.filter((product) => Number(product.stock) <= 5).length;
    const paidRevenue = orders
      .filter((order) => order.payment_status === "Paid")
      .reduce((total, order) => total + Number(order.total_amount), 0);

    return [
      {
        label: "Paid Revenue",
        value: new Intl.NumberFormat("id-ID", {
          style: "currency",
          currency: "IDR",
          maximumFractionDigits: 0,
        }).format(paidRevenue),
        note: "Calculated from paid orders currently returned by the live backend.",
      },
      {
        label: "Active Orders",
        value: String(orderListData?.pagination?.total_items ?? orders.length),
        note: "Live order queue from the backend sales module.",
      },
      {
        label: "Published Articles",
        value: String(publishedArticles),
        note: "Published SEO content currently available in the article API.",
      },
      {
        label: "Low Stock Alerts",
        value: `${lowStockCount} SKU`,
        note: "Products with stock at 5 units or below from the live product list.",
      },
    ];
  }, [articleListData?.data?.length, articleListData?.pagination?.total_items, orderListData?.data, orderListData?.pagination?.total_items, productListData?.data]);

  return (
    <>
      <PageHeader
        title={`Welcome back, ${user?.full_name ?? "User"}`}
        subtitle="Monitor your IT e-commerce operation across storefront content, order flow, and internal admin modules."
        breadcrumbs={[
          { label: "Admin", route: undefined },
          { label: "Dashboard", route: undefined },
        ]}
      />

      <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        {dashboardStats.map((stat) => (
          <article
            key={stat.label}
            className="rounded-3xl border border-primary-100 bg-gradient-to-br from-white via-white to-primary-50/80 p-5 shadow-sm"
          >
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-primary-700">
              {stat.label}
            </p>
            <p className="mt-3 text-3xl font-semibold tracking-tight text-dark-900">{stat.value}</p>
            <p className="mt-3 text-sm leading-6 text-dark-500">{stat.note}</p>
          </article>
        ))}
      </section>

      <section className="mt-6 grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <article className="rounded-3xl border border-dark-200 bg-white p-6 shadow-sm">
          <div className="flex items-start justify-between gap-4">
            <div>
              <Badge variant="primary-outline">Catalog Focus</Badge>
              <h2 className="mt-3 text-xl font-semibold text-dark-900">Featured SKUs driving attention</h2>
              <p className="mt-2 text-sm text-dark-500">
                Featured items from the live product catalog that can feed landing pages, ads, and bundle campaigns.
              </p>
            </div>
          </div>

          <div className="mt-5 grid gap-4 lg:grid-cols-2">
            {featuredProducts.length === 0 ? (
              <article className="rounded-2xl border border-dark-200 bg-light-50 p-4 lg:col-span-2">
                <p className="text-sm text-dark-500">
                  No featured products available yet. Mark products as featured from the catalog module to surface them here.
                </p>
              </article>
            ) : featuredProducts.map((product) => (
              <article key={product.id} className="rounded-2xl border border-primary-100 bg-primary-50/40 p-4">
                <div className="flex items-center justify-between gap-2">
                  <Badge variant="secondary-outline">{product.category_name}</Badge>
                  <Badge
                    variant={
                      product.stock_badge === "Ready Stock"
                        ? "success-outline"
                        : product.stock_badge === "Pre Order"
                          ? "warning-outline"
                          : "primary-outline"
                    }
                  >
                    {product.stock_badge}
                  </Badge>
                </div>
                <h3 className="mt-3 text-lg font-semibold text-dark-900">{product.name}</h3>
                <p className="mt-2 text-sm leading-6 text-dark-500">{product.highlight || product.summary || "-"}</p>
                <p className="mt-4 text-base font-semibold text-primary-700">
                  {new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR",
                    maximumFractionDigits: 0,
                  }).format(Number(product.price))}
                </p>
              </article>
            ))}
          </div>
        </article>

        <div className="grid gap-6">
          <article className="rounded-3xl border border-secondary-100 bg-secondary-50/70 p-6 shadow-sm">
            <div className="flex items-start gap-3">
              <PackageCheck className="mt-1 h-5 w-5 text-secondary-600" />
              <div>
                <h2 className="text-xl font-semibold text-dark-900">Customer journey split</h2>
                <p className="mt-2 text-sm leading-6 text-dark-500">
                  SEO pages stay in CodeIgniter for discoverability, while cart, order, and admin flows stay in React under <span className="font-semibold text-dark-700">/app</span>.
                </p>
              </div>
            </div>
          </article>

          <article className="rounded-3xl border border-dark-200 bg-dark-900 p-6 text-white shadow-sm">
            <div className="flex items-start gap-3">
              <Boxes className="mt-1 h-5 w-5 text-primary-300" />
              <div>
                <h2 className="text-xl font-semibold">Suggested next backend APIs</h2>
                <ul className="mt-4 space-y-3 text-sm leading-6 text-white/80">
                  <li>Expand products with brand, gallery, and richer storefront filtering.</li>
                  <li>Expand orders with payment proof, shipment tracking, and courier metadata.</li>
                  <li>Expand articles with tags, hero placement, and author-level publishing workflow.</li>
                </ul>
              </div>
            </div>
          </article>

          <article className="rounded-3xl border border-primary-100 bg-white p-6 shadow-sm">
            <div className="flex items-start gap-3">
              <FileSearch className="mt-1 h-5 w-5 text-primary-600" />
              <div>
                <h2 className="text-xl font-semibold text-dark-900">Quick validation</h2>
                <p className="mt-2 text-sm leading-6 text-dark-500">
                  Use the customer and admin route groups to verify the `/app/customer/*` and `/app/admin/*` split against live APIs and seeded backend data.
                </p>
              </div>
            </div>
          </article>
        </div>
      </section>
    </>
  );
};
