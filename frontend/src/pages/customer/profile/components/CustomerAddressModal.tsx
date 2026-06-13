import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Button, FormInput, Modal } from "../../../../components/ui";
import type { CustomerSavedAddress } from "../../../../store/customerProfileStore";
import { customerAddressSchema, type CustomerAddressSchemaType } from "../schema/CustomerAddressSchema";

interface CustomerAddressModalProps {
  open: boolean;
  mode: "create" | "edit";
  initialValue?: CustomerSavedAddress | null;
  onClose: () => void;
  onSubmitAddress: (values: CustomerAddressSchemaType) => void;
}

const booleanOptions = [
  { value: "1", label: "Yes" },
  { value: "0", label: "No" },
];

export const CustomerAddressModal = ({
  open,
  mode,
  initialValue,
  onClose,
  onSubmitAddress,
}: CustomerAddressModalProps) => {
  const { control, handleSubmit, reset } = useForm<CustomerAddressSchemaType>({
    resolver: zodResolver(customerAddressSchema),
    defaultValues: {
      label: "",
      recipient_name: "",
      phone: "",
      address: "",
      is_default: "0",
    },
  });

  useEffect(() => {
    if (!open) {
      return;
    }

    if (mode === "edit" && initialValue) {
      reset({
        label: initialValue.label,
        recipient_name: initialValue.recipient_name,
        phone: initialValue.phone,
        address: initialValue.address,
        is_default: initialValue.is_default ? "1" : "0",
      });
      return;
    }

    reset({
      label: "",
      recipient_name: "",
      phone: "",
      address: "",
      is_default: "0",
    });
  }, [initialValue, mode, open, reset]);

  return (
    <Modal
      open={open}
      onClose={onClose}
      title={mode === "create" ? "Add saved address" : "Edit saved address"}
      subtitle="Keep a ready-to-use delivery address for faster checkout."
      className="max-w-3xl"
      footer={
        <div className="flex justify-end gap-3">
          <Button type="button" variant="light-outline" onClick={onClose}>
            Cancel
          </Button>
          <Button type="button" buttonType="submit" onClick={handleSubmit(onSubmitAddress)}>
            {mode === "create" ? "Save Address" : "Update Address"}
          </Button>
        </div>
      }
    >
      <form className="grid gap-4 md:grid-cols-2" onSubmit={handleSubmit(onSubmitAddress)}>
        <FormInput control={control} name="label" label="Address Label" placeholder="Home Office" />
        <FormInput control={control} name="recipient_name" label="Recipient Name" placeholder="John Doe" />
        <FormInput control={control} name="phone" label="Phone Number" placeholder="0812xxxxxxx" />
        <FormInput
          control={control}
          name="is_default"
          type="dropdown"
          label="Set as Default"
          placeholder="Choose default option"
          dropdownOptions={booleanOptions}
        />
        <FormInput
          control={control}
          name="address"
          type="textarea"
          label="Full Address"
          placeholder="Street, district, city, province, postal code"
          className="md:col-span-2"
        />
      </form>
    </Modal>
  );
};
