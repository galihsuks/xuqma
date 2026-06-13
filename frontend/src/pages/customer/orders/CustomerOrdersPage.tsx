import { Clock3, PackageCheck, Wallet } from "lucide-react";
import { useMemo } from "react";
import { PageHeader } from "../../../components/layout/PageHeader";
import { Button, Table } from "../../../components/ui";
import { useOrderListQuery } from "../../../api/order/orderQuery";
import { DEFAULT_PAGE_SIZE } from "../../../constants";
import { usePageTitle } from "../../../hooks/usePageTitle";
import { getCustomerOrderTableColumns } from "./components/CustomerOrderTableColumns";

const formatCurrency = (value: number) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(value);

export const CustomerOrdersPage = () => {
  usePageTitle("Orders");

  const columns = getCustomerOrderTableColumns();

  const { data, isPending } = useOrderListQuery({
    page: 1,
    page_size: DEFAULT_PAGE_SIZE,
  });

  const summary = useMemo(() => {
    const items = data?.data ?? [];
    return {
      pendingPayment: items.filter((item) => item.status === "Waiting Payment").length,
      fulfillmentQueue: items.filter((item) => ["Processing", "Packed", "Shipped"].includes(item.status))
        .length,
      totalValue: items.reduce((total, item) => total + Number(item.total_amount), 0),
    };
  }, [data?.data]);

  return (
    <>
      <PageHeader
        title="Active Orders"
        subtitle="Track payment confirmation, packing, and delivery progress from your real order records."
        breadcrumbs={[
          { label: "Customer", route: undefined },
          { label: "Orders", route: undefined },
        ]}
      />

      <div className="mb-6 grid gap-4 md:grid-cols-3">
        <div className="rounded-3xl border border-warning-100 bg-warning-50/80 p-5">
          <Clock3 className="h-5 w-5 text-warning-600" />
          <p className="mt-4 text-sm text-dark-500">Pending payment</p>
          <p className="mt-1 text-2xl font-semibold text-dark-900">{summary.pendingPayment}</p>
        </div>
        <div className="rounded-3xl border border-info-100 bg-info-50/80 p-5">
          <PackageCheck className="h-5 w-5 text-info-600" />
          <p className="mt-4 text-sm text-dark-500">Fulfillment queue</p>
          <p className="mt-1 text-2xl font-semibold text-dark-900">{summary.fulfillmentQueue}</p>
        </div>
        <div className="rounded-3xl border border-secondary-100 bg-secondary-50/80 p-5">
          <Wallet className="h-5 w-5 text-secondary-600" />
          <p className="mt-4 text-sm text-dark-500">Current total value</p>
          <p className="mt-1 text-2xl font-semibold text-dark-900">{formatCurrency(summary.totalValue)}</p>
        </div>
      </div>

      <Table
        columns={columns}
        data={data?.data ?? []}
        loading={isPending}
        emptyText="You do not have any active orders yet."
        showNumber={false}
      />

      <div className="mt-5 flex justify-end">
        <Button variant="primary-outline">Need payment instruction?</Button>
      </div>
    </>
  );
};
