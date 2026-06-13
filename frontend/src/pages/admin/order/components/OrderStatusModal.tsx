import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useQueryClient } from "@tanstack/react-query";
import { Button, FormInput, Modal } from "../../../../components/ui";
import { queryKeys } from "../../../../api/queryKeys";
import { useOrderDetailQuery, useUpdateOrderMutation } from "../../../../api/order/orderQuery";
import { useApiFormError } from "../../../../hooks/useApiFormError";
import { useNotificationStore } from "../../../../store/notifStore";
import { orderStatusFormSchema, type OrderStatusFormSchemaType } from "../schema/OrderStatusFormSchema";

interface OrderStatusModalProps {
  open: boolean;
  orderId: string | null;
  onClose: () => void;
}

const orderStatusOptions = [
  { value: "Waiting Payment", label: "Waiting Payment" },
  { value: "Processing", label: "Processing" },
  { value: "Packed", label: "Packed" },
  { value: "Shipped", label: "Shipped" },
  { value: "Completed", label: "Completed" },
  { value: "Cancelled", label: "Cancelled" },
];

const paymentStatusOptions = [
  { value: "Unpaid", label: "Unpaid" },
  { value: "Paid", label: "Paid" },
  { value: "Refunded", label: "Refunded" },
];

export const OrderStatusModal = ({ open, orderId, onClose }: OrderStatusModalProps) => {
  const queryClient = useQueryClient();
  const { addToast } = useNotificationStore();
  const { handleApiFormError } = useApiFormError({ logEvent: "order_status_update_failed" });
  const { mutate: updateOrderMutation, isPending: isUpdateOrderPending } = useUpdateOrderMutation();
  const { data: orderDetailData, isPending: isOrderDetailPending } = useOrderDetailQuery(
    open ? (orderId ?? "") : "",
  );

  const { control, handleSubmit, reset } = useForm<OrderStatusFormSchemaType>({
    resolver: zodResolver(orderStatusFormSchema),
    defaultValues: {
      status: "Waiting Payment",
      payment_status: "Unpaid",
      notes: "",
    },
  });

  useEffect(() => {
    if (!open || !orderDetailData?.data) {
      return;
    }

    reset({
      status: orderDetailData.data.status,
      payment_status: orderDetailData.data.payment_status,
      notes: orderDetailData.data.notes ?? "",
    });
  }, [open, orderDetailData?.data, reset]);

  const onSubmit = (values: OrderStatusFormSchemaType) => {
    const order = orderDetailData?.data;
    if (!orderId || !order) {
      return;
    }

    updateOrderMutation(
      {
        id: orderId,
        payload: {
          user_id: order.user_id,
          customer_name: order.customer_name,
          customer_email: order.customer_email,
          customer_phone: order.customer_phone,
          channel: order.channel,
          status: values.status,
          payment_status: values.payment_status,
          notes: values.notes || null,
          items: (order.items ?? []).map((item) => ({
            product_id: item.product_id,
            product_name: item.product_name,
            qty: item.qty,
            unit_price: item.unit_price,
          })),
        },
      },
      {
        onSuccess: (response) => {
          addToast(response.message, "success");
          void queryClient.invalidateQueries({ queryKey: ["order"] });
          void queryClient.invalidateQueries({ queryKey: queryKeys.order.detail(orderId) });
          onClose();
        },
        onError: (error) => {
          handleApiFormError(error, {
            order_id: orderId,
            next_status: values.status,
            next_payment_status: values.payment_status,
          });
        },
      },
    );
  };

  return (
    <Modal
      open={open}
      onClose={onClose}
      title="Update order status"
      subtitle="Adjust fulfillment and payment progress for this order."
      className="max-w-3xl"
      footer={
        <div className="flex justify-end gap-3">
          <Button type="button" variant="light-outline" onClick={onClose}>
            Cancel
          </Button>
          <Button
            type="button"
            buttonType="submit"
            loading={isUpdateOrderPending}
            onClick={handleSubmit(onSubmit)}
          >
            Save Status
          </Button>
        </div>
      }
    >
      {isOrderDetailPending || !orderDetailData?.data ? (
        <div className="grid gap-3 md:grid-cols-2">
          {Array.from({ length: 4 }).map((_, index) => (
            <div key={index} className="h-11 rounded-xl bg-light-100" />
          ))}
        </div>
      ) : (
        <form className="grid gap-4 md:grid-cols-2" onSubmit={handleSubmit(onSubmit)}>
          <FormInput
            control={control}
            name="status"
            type="dropdown"
            label="Order Status"
            placeholder="Select order status"
            dropdownOptions={orderStatusOptions}
          />
          <FormInput
            control={control}
            name="payment_status"
            type="dropdown"
            label="Payment Status"
            placeholder="Select payment status"
            dropdownOptions={paymentStatusOptions}
          />
          <FormInput
            control={control}
            name="notes"
            type="textarea"
            label="Internal / Customer Notes"
            placeholder="Add tracking update, payment verification note, or fulfillment remark"
            className="md:col-span-2"
          />
        </form>
      )}
    </Modal>
  );
};
