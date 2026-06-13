import { Box, CreditCard, PackageCheck, ReceiptText, Truck } from "lucide-react";
import { useParams } from "react-router-dom";
import { PageHeader } from "../../../components/layout/PageHeader";
import { Badge, Button } from "../../../components/ui";
import { useOrderDetailQuery } from "../../../api/order/orderQuery";
import { usePageTitle } from "../../../hooks/usePageTitle";
import type { Order } from "../../../interfaces/order";
import InternalServerError from "../../../components/templates/InternalServerError";

const statusVariantMap: Record<
  Order["status"],
  | "warning-outline"
  | "info-outline"
  | "primary-outline"
  | "secondary-outline"
  | "success-outline"
  | "danger-outline"
> = {
  "Waiting Payment": "warning-outline",
  Processing: "info-outline",
  Packed: "primary-outline",
  Shipped: "secondary-outline",
  Completed: "success-outline",
  Cancelled: "danger-outline",
};

const paymentVariantMap: Record<
  Order["payment_status"],
  "warning-outline" | "success-outline" | "danger-outline"
> = {
  Unpaid: "warning-outline",
  Paid: "success-outline",
  Refunded: "danger-outline",
};

const formatCurrency = (value: number | string) => {
  const amount = typeof value === "number" ? value : Number(value);
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number.isNaN(amount) ? 0 : amount);
};

export const CustomerOrderDetailPage = () => {
  const params = useParams();
  const orderId = params.id ?? "";

  usePageTitle("Order Detail");

  const { data, isPending, error } = useOrderDetailQuery(orderId);
  const order = data?.data;

  if (error) {
    return <InternalServerError />;
  }

  return (
    <>
      <PageHeader
        showGoBack
        title={order ? order.order_number : "Order Detail"}
        subtitle="Review payment state, fulfillment progress, and the exact items included in this order."
        breadcrumbs={[
          { label: "Customer", route: undefined },
          { label: "Orders", route: "/customer/orders" },
          { label: "Detail", route: undefined },
        ]}
      />

      {isPending || !order ? (
        <div className="grid gap-4">
          {Array.from({ length: 3 }).map((_, index) => (
            <div key={index} className="h-28 rounded-3xl bg-light-100" />
          ))}
        </div>
      ) : (
        <div className="space-y-6">
          <div className="grid gap-4 md:grid-cols-3">
            <div className="rounded-3xl border border-warning-100 bg-warning-50/70 p-5">
              <CreditCard className="h-5 w-5 text-warning-600" />
              <p className="mt-4 text-sm text-dark-500">Payment status</p>
              <div className="mt-2">
                <Badge variant={paymentVariantMap[order.payment_status]}>{order.payment_status}</Badge>
              </div>
            </div>
            <div className="rounded-3xl border border-info-100 bg-info-50/70 p-5">
              <Truck className="h-5 w-5 text-info-600" />
              <p className="mt-4 text-sm text-dark-500">Fulfillment status</p>
              <div className="mt-2">
                <Badge variant={statusVariantMap[order.status]}>{order.status}</Badge>
              </div>
            </div>
            <div className="rounded-3xl border border-secondary-100 bg-secondary-50/70 p-5">
              <ReceiptText className="h-5 w-5 text-secondary-600" />
              <p className="mt-4 text-sm text-dark-500">Order total</p>
              <p className="mt-1 text-2xl font-semibold text-dark-900">{formatCurrency(order.total_amount)}</p>
            </div>
          </div>

          <div className="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <section className="rounded-3xl border border-primary-100 bg-white p-6">
              <div className="flex items-center gap-3">
                <div className="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                  <Box className="h-5 w-5" />
                </div>
                <div>
                  <h2 className="text-lg font-semibold text-dark-900">Items in this order</h2>
                  <p className="text-sm text-dark-500">{order.total_items} item(s) recorded in the backend.</p>
                </div>
              </div>

              <div className="mt-5 space-y-4">
                {(order.items ?? []).map((item) => (
                  <article
                    key={item.id}
                    className="rounded-2xl border border-primary-100 bg-gradient-to-r from-white to-primary-50/60 p-4"
                  >
                    <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                      <div>
                        <p className="font-semibold text-dark-900">{item.product_name}</p>
                        <p className="mt-1 text-sm text-dark-500">
                          {item.qty} x {formatCurrency(item.unit_price)}
                        </p>
                      </div>
                      <p className="text-lg font-semibold text-primary-700">
                        {formatCurrency(item.subtotal)}
                      </p>
                    </div>
                  </article>
                ))}
              </div>
            </section>

            <aside className="space-y-4">
              <div className="rounded-3xl border border-dark-200 bg-dark-900 p-6 text-white">
                <p className="text-xs font-semibold uppercase tracking-[0.18em] text-primary-300">
                  Order Snapshot
                </p>
                <div className="mt-5 space-y-3 text-sm text-white/80">
                  <div className="flex items-center justify-between">
                    <span>Customer</span>
                    <span>{order.customer_name}</span>
                  </div>
                  <div className="flex items-center justify-between gap-3">
                    <span>Email</span>
                    <span className="text-right">{order.customer_email || "-"}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span>Channel</span>
                    <span>{order.channel}</span>
                  </div>
                  <div className="flex items-center justify-between gap-3">
                    <span>Created at</span>
                    <span className="text-right">{order.created_at || "-"}</span>
                  </div>
                </div>
                <div className="mt-5 border-t border-white/10 pt-5">
                  <p className="text-sm text-white/60">Notes</p>
                  <p className="mt-2 text-sm text-white/85">{order.notes || "No additional notes."}</p>
                </div>
              </div>

              <div className="rounded-3xl border border-success-100 bg-success-50/70 p-5">
                <div className="flex items-start gap-3">
                  <PackageCheck className="mt-0.5 h-5 w-5 text-success-600" />
                  <div>
                    <p className="font-semibold text-dark-900">Tracking-ready structure</p>
                    <p className="mt-1 text-sm text-dark-500">
                      This detail page is already reading the real order payload from the backend API.
                    </p>
                  </div>
                </div>
                <Button type="link" link="/customer/history" variant="success-outline" className="mt-4">
                  Open Completed History
                </Button>
              </div>
            </aside>
          </div>
        </div>
      )}
    </>
  );
};
