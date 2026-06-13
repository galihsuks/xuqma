import { ReceiptText, Search } from "lucide-react";
import { useEffect, useMemo, useState } from "react";
import { useForm } from "react-hook-form";
import { PageHeader } from "../../../components/layout/PageHeader";
import { FilterGrid, FormInput, Table } from "../../../components/ui";
import { useOrderListQuery } from "../../../api/order/orderQuery";
import { DEFAULT_PAGE_SIZE } from "../../../constants";
import { useDebounce } from "../../../hooks/useDebounce";
import { usePageTitle } from "../../../hooks/usePageTitle";
import type { Order } from "../../../interfaces/order";
import InternalServerError from "../../../components/templates/InternalServerError";
import { useHasAccess } from "../../../store/accessControlStore";
import { getOrderTableColumns } from "./components/OrderTableColumns";
import { OrderStatusModal } from "./components/OrderStatusModal";

interface OrderFilterSchemaType {
  keywords: string;
  status: string;
}

const orderStatusOptions = [
  { value: "", label: "All statuses" },
  { value: "Waiting Payment", label: "Waiting Payment" },
  { value: "Processing", label: "Processing" },
  { value: "Packed", label: "Packed" },
  { value: "Shipped", label: "Shipped" },
  { value: "Completed", label: "Completed" },
  { value: "Cancelled", label: "Cancelled" },
];

export const OrderPage = () => {
  usePageTitle("Order Management");

  const hasAccess = useHasAccess();
  const [page, setPage] = useState(1);
  const [selectedOrderId, setSelectedOrderId] = useState<string | null>(null);
  const { control, watch } = useForm<OrderFilterSchemaType>({
    defaultValues: {
      keywords: "",
      status: "",
    },
  });
  const keywords = watch("keywords");
  const status = watch("status");
  const debouncedKeywords = useDebounce(keywords, 300);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeywords, status]);

  const { data, isPending, error } = useOrderListQuery({
    page,
    page_size: DEFAULT_PAGE_SIZE,
    keywords: debouncedKeywords || undefined,
    status:
      (status as
        | ""
        | "Waiting Payment"
        | "Processing"
        | "Packed"
        | "Shipped"
        | "Completed"
        | "Cancelled") || undefined,
  });

  const orderSummary = useMemo(() => {
    const items = data?.data ?? [];
    return {
      totalOrders: items.length,
      waitingPayment: items.filter((item) => item.status === "Waiting Payment").length,
      paidOrders: items.filter((item) => item.payment_status === "Paid").length,
    };
  }, [data?.data]);

  if (error) {
    return <InternalServerError />;
  }

  const orderColumns = getOrderTableColumns({
    hasAccess,
    onUpdate: (order: Order) => setSelectedOrderId(order.id),
  });

  return (
    <>
      <PageHeader
        title="Order Management"
        subtitle="Monitor live order queue data from the backend, including payment and fulfillment states."
        breadcrumbs={[
          { label: "Admin", route: undefined },
          { label: "Sales", route: undefined },
          { label: "Orders", route: undefined },
        ]}
      />

      <div className="mb-6 grid gap-4 md:grid-cols-3">
        <div className="rounded-3xl border border-primary-100 bg-primary-50/80 p-5">
          <ReceiptText className="h-5 w-5 text-primary-600" />
          <p className="mt-4 text-sm text-dark-500">Orders in current page</p>
          <p className="mt-1 text-2xl font-semibold text-dark-900">{orderSummary.totalOrders}</p>
        </div>
        <div className="rounded-3xl border border-warning-100 bg-warning-50/80 p-5">
          <p className="text-sm text-dark-500">Waiting payment</p>
          <p className="mt-1 text-2xl font-semibold text-dark-900">{orderSummary.waitingPayment}</p>
        </div>
        <div className="rounded-3xl border border-success-100 bg-success-50/80 p-5">
          <p className="text-sm text-dark-500">Paid orders in current page</p>
          <p className="mt-1 text-2xl font-semibold text-dark-900">{orderSummary.paidOrders}</p>
        </div>
      </div>

      <FilterGrid>
        <FormInput
          control={control}
          name="keywords"
          type="text"
          icon={Search}
          placeholder="Search by order number, customer, email, or channel..."
        />
        <FormInput
          control={control}
          name="status"
          type="dropdown"
          placeholder="Filter by status"
          dropdownOptions={orderStatusOptions}
        />
      </FilterGrid>

      <Table
        columns={orderColumns}
        data={data?.data ?? []}
        loading={isPending}
        emptyText="No orders available yet."
        pagination={data?.pagination}
        onPageChange={setPage}
      />

      <OrderStatusModal
        open={selectedOrderId !== null}
        orderId={selectedOrderId}
        onClose={() => setSelectedOrderId(null)}
      />
    </>
  );
};
