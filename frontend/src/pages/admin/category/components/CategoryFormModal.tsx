import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useQueryClient } from "@tanstack/react-query";
import { Button, FormInput, Modal } from "../../../../components/ui";
import {
  useCreateProductCategoryMutation,
  useProductCategoryDetailQuery,
  useUpdateProductCategoryMutation,
} from "../../../../api/productCategory/productCategoryQuery";
import { queryKeys } from "../../../../api/queryKeys";
import { useApiFormError } from "../../../../hooks/useApiFormError";
import { useNotificationStore } from "../../../../store/notifStore";
import { categoryFormSchema, type CategoryFormSchemaType } from "../schema/CategoryFormSchema";

interface CategoryFormModalProps {
  open: boolean;
  mode: "create" | "edit";
  categoryId?: string | null;
  onClose: () => void;
}

const booleanOptions = [
  { value: "1", label: "Yes" },
  { value: "0", label: "No" },
];

export const CategoryFormModal = ({
  open,
  mode,
  categoryId,
  onClose,
}: CategoryFormModalProps) => {
  const queryClient = useQueryClient();
  const { addToast } = useNotificationStore();
  const { mutate: createCategoryMutation, isPending: isCreateCategoryPending } =
    useCreateProductCategoryMutation();
  const { mutate: updateCategoryMutation, isPending: isUpdateCategoryPending } =
    useUpdateProductCategoryMutation();
  const { handleApiFormError } = useApiFormError({ logEvent: "category_form_submit_failed" });
  const { data: categoryDetailData, isPending: isCategoryDetailPending } =
    useProductCategoryDetailQuery(mode === "edit" ? (categoryId ?? "") : "");

  const { control, handleSubmit, reset } = useForm<CategoryFormSchemaType>({
    resolver: zodResolver(categoryFormSchema),
    defaultValues: {
      name: "",
      slug: "",
      description: "",
      icon: "",
      display: "1",
      sort: "0",
    },
  });

  useEffect(() => {
    if (!open) {
      return;
    }

    if (mode === "create") {
      reset({
        name: "",
        slug: "",
        description: "",
        icon: "",
        display: "1",
        sort: "0",
      });
      return;
    }

    if (!categoryDetailData?.data) {
      return;
    }

    reset({
      name: categoryDetailData.data.name,
      slug: categoryDetailData.data.slug,
      description: categoryDetailData.data.description ?? "",
      icon: categoryDetailData.data.icon ?? "",
      display: String(
        categoryDetailData.data.display === true || categoryDetailData.data.display === "1"
          ? "1"
          : "0",
      ) as "1" | "0",
      sort: String(categoryDetailData.data.sort ?? "0"),
    });
  }, [categoryDetailData?.data, mode, open, reset]);

  const isSubmitPending = isCreateCategoryPending || isUpdateCategoryPending;

  const onSubmit = (values: CategoryFormSchemaType) => {
    const payload = {
      name: values.name,
      slug: values.slug,
      description: values.description || null,
      icon: values.icon || null,
      display: values.display,
      sort: Number(values.sort),
    };

    if (mode === "create") {
      createCategoryMutation(payload, {
        onSuccess: (response) => {
          addToast(response.message, "success");
          void queryClient.invalidateQueries({ queryKey: ["product-category"] });
          onClose();
        },
        onError: (error) => {
          handleApiFormError(error, {
            form_mode: mode,
            category_slug: values.slug,
          });
        },
      });
      return;
    }

    if (!categoryId) {
      return;
    }

    updateCategoryMutation(
      { id: categoryId, payload },
      {
        onSuccess: (response) => {
          addToast(response.message, "success");
          void queryClient.invalidateQueries({ queryKey: ["product-category"] });
          void queryClient.invalidateQueries({
            queryKey: queryKeys.productCategory.detail(categoryId),
          });
          onClose();
        },
        onError: (error) => {
          handleApiFormError(error, {
            form_mode: mode,
            category_id: categoryId,
            category_slug: values.slug,
          });
        },
      },
    );
  };

  return (
    <Modal
      open={open}
      onClose={onClose}
      title={mode === "create" ? "Create category" : "Edit category"}
      subtitle={
        mode === "create"
          ? "Add a new product grouping for the storefront and admin catalog."
          : "Update the selected category details."
      }
      className="max-w-3xl"
      footer={
        <div className="flex justify-end gap-3">
          <Button type="button" variant="light-outline" onClick={onClose}>
            Cancel
          </Button>
          <Button
            type="button"
            buttonType="submit"
            loading={isSubmitPending}
            onClick={handleSubmit(onSubmit)}
          >
            {mode === "create" ? "Save Category" : "Update Category"}
          </Button>
        </div>
      }
    >
      {mode === "edit" && isCategoryDetailPending ? (
        <div className="grid gap-3 md:grid-cols-2">
          {Array.from({ length: 6 }).map((_, index) => (
            <div key={index} className="h-11 rounded-xl bg-light-100" />
          ))}
        </div>
      ) : (
        <form className="grid gap-4 md:grid-cols-2" onSubmit={handleSubmit(onSubmit)}>
          <FormInput
            control={control}
            name="name"
            label="Category Name"
            placeholder="Gaming Peripherals"
          />
          <FormInput
            control={control}
            name="slug"
            label="Slug"
            placeholder="gaming-peripherals"
          />
          <FormInput
            control={control}
            name="icon"
            label="Icon"
            placeholder="bi-headset-vr"
          />
          <FormInput control={control} name="sort" type="number" label="Sort Order" placeholder="0" />
          <FormInput
            control={control}
            name="display"
            type="dropdown"
            label="Display on Storefront"
            placeholder="Select display option"
            dropdownOptions={booleanOptions}
          />
          <div className="hidden md:block" />
          <FormInput
            control={control}
            name="description"
            label="Description"
            placeholder="Category description for storefront sections"
            className="md:col-span-2"
          />
        </form>
      )}
    </Modal>
  );
};
