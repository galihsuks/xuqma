import { Eye, RotateCcw, Star } from "lucide-react";
import { PageHeader } from "../../../components/layout/PageHeader";
import { Badge, Button } from "../../../components/ui";
import { useOrderListQuery } from "../../../api/order/orderQuery";
import { DEFAULT_PAGE_SIZE } from "../../../constants";
import { usePageTitle } from "../../../hooks/usePageTitle";

const formatCurrency = (value: number | string) => {
  const amount = typeof value === "number" ? value : Number(value);
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number.isNaN(amount) ? 0 : amount);
};

export const CustomerHistoryPage = () => {
  usePageTitle("Order History");

  const { data, isPending } = useOrderListQuery({
    page: 1,
    page_size: DEFAULT_PAGE_SIZE,
    status: "Completed",
  });

  const items = data?.data ?? [];

  return (
    <>
      <PageHeader
        title="Purchase History"
        subtitle="Review completed orders from your account and use them as the base for future repeat-order actions."
        breadcrumbs={[
          { label: "Customer", route: undefined },
          { label: "History", route: undefined },
        ]}
      />

      {isPending ? (
        <div className="grid gap-4">
          {Array.from({ length: 2 }).map((_, index) => (
            <div key={index} className="h-40 rounded-3xl bg-light-100" />
          ))}
        </div>
      ) : items.length === 0 ? (
        <article className="rounded-3xl border border-dark-200 bg-white p-6 text-center">
          <h2 className="text-xl font-semibold text-dark-900">No completed orders yet</h2>
          <p className="mt-2 text-sm text-dark-500">
            Once an order reaches completed status, it will appear here for reorder and review actions.
          </p>
        </article>
      ) : (
        <div className="grid gap-4">
          {items.map((item) => (
            <article key={item.id} className="rounded-3xl border border-dark-200 bg-white p-5">
              <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                  <div className="flex flex-wrap items-center gap-2">
                    <Badge variant="success-outline">{item.status}</Badge>
                    <span className="text-xs font-semibold uppercase tracking-[0.18em] text-dark-400">
                      {item.order_number}
                    </span>
                  </div>
                  <h2 className="mt-3 text-xl font-semibold text-dark-900">{item.customer_name}</h2>
                  <p className="mt-1 text-sm text-dark-500">Completed on {item.updated_at || item.created_at}</p>
                </div>
                <div className="flex flex-wrap gap-2">
                  <Button variant="light-outline" icon={RotateCcw}>
                    Buy Again
                  </Button>
                  <Button type="link" link={`/customer/history/${item.id}`} variant="primary-outline" icon={Eye}>
                    Detail
                  </Button>
                  <Button variant="secondary-outline" icon={Star}>
                    Leave Review
                  </Button>
                </div>
              </div>
              <div className="mt-4 border-t border-dark-100 pt-4">
                <p className="text-sm text-dark-500">Order total</p>
                <p className="text-lg font-semibold text-primary-700">{formatCurrency(item.total_amount)}</p>
              </div>
            </article>
          ))}
        </div>
      )}
    </>
  );
};
