import { useEffect, useMemo } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useQueryClient } from "@tanstack/react-query";
import { Button, FormInput, Modal } from "../../../../components/ui";
import {
  useCreateProductMutation,
  useProductDetailQuery,
  useUpdateProductMutation,
} from "../../../../api/product/productQuery";
import { productCategoryApi } from "../../../../api/productCategory/productCategoryApi";
import { queryKeys } from "../../../../api/queryKeys";
import { useApiFormError } from "../../../../hooks/useApiFormError";
import { useNotificationStore } from "../../../../store/notifStore";
import { productFormSchema, type ProductFormSchemaType } from "../schema/ProductFormSchema";

interface ProductFormModalProps {
  open: boolean;
  mode: "create" | "edit";
  productId?: string | null;
  onClose: () => void;
}

const stockBadgeOptions = [
  { value: "Ready Stock", label: "Ready Stock" },
  { value: "Pre Order", label: "Pre Order" },
  { value: "Limited", label: "Limited" },
];

const booleanOptions = [
  { value: "1", label: "Yes" },
  { value: "0", label: "No" },
];

export const ProductFormModal = ({ open, mode, productId, onClose }: ProductFormModalProps) => {
  const queryClient = useQueryClient();
  const { addToast } = useNotificationStore();
  const { mutate: createProductMutation, isPending: isCreateProductPending } = useCreateProductMutation();
  const { mutate: updateProductMutation, isPending: isUpdateProductPending } = useUpdateProductMutation();
  const { handleApiFormError } = useApiFormError({ logEvent: "product_form_submit_failed" });
  const { data: productDetailData, isPending: isProductDetailPending } = useProductDetailQuery(
    mode === "edit" ? (productId ?? "") : "",
  );

  const { control, handleSubmit, reset } = useForm<ProductFormSchemaType>({
    resolver: zodResolver(productFormSchema),
    defaultValues: {
      category_id: "",
      sku: "",
      name: "",
      slug: "",
      summary: "",
      description: "",
      highlight: "",
      price: "",
      stock: "",
      stock_badge: "Ready Stock",
      is_featured: "0",
      display: "1",
      sort: "0",
    },
  });

  useEffect(() => {
    if (!open) return;

    if (mode === "create") {
      reset({
        category_id: "",
        sku: "",
        name: "",
        slug: "",
        summary: "",
        description: "",
        highlight: "",
        price: "",
        stock: "",
        stock_badge: "Ready Stock",
        is_featured: "0",
        display: "1",
        sort: "0",
      });
      return;
    }

    if (!productDetailData?.data) return;

    reset({
      category_id: productDetailData.data.category_id,
      sku: productDetailData.data.sku,
      name: productDetailData.data.name,
      slug: productDetailData.data.slug,
      summary: productDetailData.data.summary ?? "",
      description: productDetailData.data.description ?? "",
      highlight: productDetailData.data.highlight ?? "",
      price: String(productDetailData.data.price ?? ""),
      stock: String(productDetailData.data.stock ?? ""),
      stock_badge: productDetailData.data.stock_badge,
      is_featured: String(productDetailData.data.is_featured === true || productDetailData.data.is_featured === "1" ? "1" : "0") as "1" | "0",
      display: String(productDetailData.data.display === true || productDetailData.data.display === "1" ? "1" : "0") as "1" | "0",
      sort: String(productDetailData.data.sort ?? "0"),
    });
  }, [mode, open, productDetailData?.data, reset]);

  const categoryOptions = useMemo(() => {
    const categoryName = productDetailData?.data?.category_name;
    const categoryId = productDetailData?.data?.category_id;

    if (!categoryName || !categoryId) {
      return [];
    }

    return [{ value: categoryId, label: categoryName }];
  }, [productDetailData?.data?.category_id, productDetailData?.data?.category_name]);

  const isSubmitPending = isCreateProductPending || isUpdateProductPending;

  const onSubmit = (values: ProductFormSchemaType) => {
    const payload = {
      category_id: values.category_id,
      sku: values.sku,
      name: values.name,
      slug: values.slug,
      summary: values.summary || null,
      description: values.description || null,
      highlight: values.highlight || null,
      price: values.price,
      stock: values.stock,
      stock_badge: values.stock_badge,
      is_featured: values.is_featured,
      display: values.display,
      sort: Number(values.sort),
    };

    if (mode === "create") {
      createProductMutation(payload, {
        onSuccess: (response) => {
          addToast(response.message, "success");
          void queryClient.invalidateQueries({ queryKey: ["product"] });
          onClose();
        },
        onError: (error) => {
          handleApiFormError(error, {
            form_mode: mode,
            product_sku: values.sku,
            product_slug: values.slug,
          });
        },
      });
      return;
    }

    if (!productId) return;

    updateProductMutation(
      { id: productId, payload },
      {
        onSuccess: (response) => {
          addToast(response.message, "success");
          void queryClient.invalidateQueries({ queryKey: ["product"] });
          void queryClient.invalidateQueries({ queryKey: queryKeys.product.detail(productId) });
          onClose();
        },
        onError: (error) => {
          handleApiFormError(error, {
            form_mode: mode,
            product_id: productId,
            product_sku: values.sku,
            product_slug: values.slug,
          });
        },
      },
    );
  };

  return (
    <Modal
      open={open}
      onClose={onClose}
      title={mode === "create" ? "Create product" : "Edit product"}
      subtitle={
        mode === "create"
          ? "Add a new storefront product to the e-commerce catalog."
          : "Update selected product information."
      }
      className="max-w-4xl"
      footer={
        <div className="flex justify-end gap-3">
          <Button type="button" variant="light-outline" onClick={onClose}>
            Cancel
          </Button>
          <Button type="button" buttonType="submit" loading={isSubmitPending} onClick={handleSubmit(onSubmit)}>
            {mode === "create" ? "Save Product" : "Update Product"}
          </Button>
        </div>
      }
    >
      {mode === "edit" && isProductDetailPending ? (
        <div className="grid gap-3 md:grid-cols-2">
          {Array.from({ length: 8 }).map((_, index) => (
            <div key={index} className="h-11 rounded-xl bg-light-100" />
          ))}
        </div>
      ) : (
        <form className="grid gap-4 md:grid-cols-2" onSubmit={handleSubmit(onSubmit)}>
          <FormInput
            control={control}
            name="category_id"
            type="dropdown"
            label="Category"
            placeholder="Select category"
            dropdownOptions={categoryOptions}
            loadDropdownOptions={async (keywords) => {
              const response = await productCategoryApi.index({
                page: 1,
                page_size: 100,
                keywords: keywords || undefined,
              });
              return (response.data ?? []).map((item) => ({
                value: item.id,
                label: item.name,
              }));
            }}
          />
          <FormInput control={control} name="sku" label="SKU" placeholder="PRD-001" />
          <FormInput control={control} name="name" label="Product Name" placeholder="VoltLink 140W GaN Charger" />
          <FormInput control={control} name="slug" label="Slug" placeholder="voltlink-140w-gan-charger" />
          <FormInput control={control} name="price" type="number" label="Price" placeholder="899000" />
          <FormInput control={control} name="stock" type="number" label="Stock" placeholder="42" />
          <FormInput
            control={control}
            name="stock_badge"
            type="dropdown"
            label="Stock Badge"
            placeholder="Select stock badge"
            dropdownOptions={stockBadgeOptions}
          />
          <FormInput
            control={control}
            name="sort"
            type="number"
            label="Sort Order"
            placeholder="0"
          />
          <FormInput
            control={control}
            name="is_featured"
            type="dropdown"
            label="Featured Product"
            placeholder="Select featured option"
            dropdownOptions={booleanOptions}
          />
          <FormInput
            control={control}
            name="display"
            type="dropdown"
            label="Display on Storefront"
            placeholder="Select display option"
            dropdownOptions={booleanOptions}
          />
          <FormInput
            control={control}
            name="highlight"
            label="Highlight"
            placeholder="Short highlight shown in cards"
            className="md:col-span-2"
          />
          <FormInput
            control={control}
            name="summary"
            label="Summary"
            placeholder="Short product summary"
            className="md:col-span-2"
          />
          <FormInput
            control={control}
            name="description"
            label="Description"
            placeholder="Detailed product description"
            className="md:col-span-2"
          />
        </form>
      )}
    </Modal>
  );
};
