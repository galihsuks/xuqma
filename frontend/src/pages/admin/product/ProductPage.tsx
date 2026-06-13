import { Boxes, Plus, Search } from "lucide-react";
import { useEffect, useMemo, useState } from "react";
import { useForm } from "react-hook-form";
import { PageHeader } from "../../../components/layout/PageHeader";
import { Button, FilterGrid, FormInput, Table } from "../../../components/ui";
import { useProductListQuery } from "../../../api/product/productQuery";
import { productCategoryApi } from "../../../api/productCategory/productCategoryApi";
import { DEFAULT_PAGE_SIZE } from "../../../constants";
import { useDebounce } from "../../../hooks/useDebounce";
import { usePageTitle } from "../../../hooks/usePageTitle";
import type { DropdownOption } from "../../../interfaces/dropdown";
import type { Product } from "../../../interfaces/product";
import InternalServerError from "../../../components/templates/InternalServerError";
import { useHasAccess } from "../../../store/accessControlStore";
import { getProductTableColumns } from "./components/ProductTableColumns";
import { ProductDeleteModal } from "./components/ProductDeleteModal";
import { ProductFormModal } from "./components/ProductFormModal";

interface ProductFilterSchemaType {
  keywords: string;
  category_id: string;
}

const ALL_CATEGORY_OPTION: DropdownOption = {
  value: "",
  label: "All categories",
};

export const ProductPage = () => {
  usePageTitle("Product Management");

  const hasAccess = useHasAccess();
  const [page, setPage] = useState(1);
  const [productFormMode, setProductFormMode] = useState<"create" | "edit" | null>(null);
  const [selectedProductId, setSelectedProductId] = useState<string | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{
    id: string;
    name: string;
    sku: string;
  } | null>(null);
  const { control, watch } = useForm<ProductFilterSchemaType>({
    defaultValues: {
      keywords: "",
      category_id: "",
    },
  });
  const keywords = watch("keywords");
  const categoryId = watch("category_id");
  const debouncedKeywords = useDebounce(keywords, 300);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeywords, categoryId]);

  const { data, isPending, error } = useProductListQuery({
    page,
    page_size: DEFAULT_PAGE_SIZE,
    keywords: debouncedKeywords || undefined,
    category_id: categoryId || undefined,
  });

  const categoryDropdownLoader = useMemo(
    () => async (filterKeywords: string) => {
      const response = await productCategoryApi.index({
        page: 1,
        page_size: 100,
        keywords: filterKeywords || undefined,
      });

      return [
        ALL_CATEGORY_OPTION,
        ...((response.data ?? []).map((item) => ({
          value: item.id,
          label: item.name,
        })) || []),
      ];
    },
    [],
  );

  if (error) {
    return <InternalServerError />;
  }

  const productColumns = getProductTableColumns({
    hasAccess,
    onEdit: (product: Product) => {
      setSelectedProductId(product.id);
      setProductFormMode("edit");
    },
    onDelete: (product: Product) =>
      setDeleteTarget({
        id: product.id,
        name: product.name,
        sku: product.sku,
      }),
  });

  return (
    <>
      <PageHeader
        title="Product Management"
        subtitle="Manage live catalog items from the backend API, including category assignment, price, and storefront stock status."
        breadcrumbs={[
          { label: "Admin", route: undefined },
          { label: "Catalog", route: undefined },
          { label: "Products", route: undefined },
        ]}
        rightElement={
          hasAccess("C") ? (
            <Button
              type="button"
              variant="primary"
              icon={Plus}
              onClick={() => {
                setSelectedProductId(null);
                setProductFormMode("create");
              }}
            >
              Add Product
            </Button>
          ) : undefined
        }
      />

      <div className="mb-6 rounded-3xl border border-primary-100 bg-gradient-to-r from-primary-50 via-white to-secondary-50 p-5">
        <div className="flex items-start gap-3">
          <div className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-600 text-white">
            <Boxes className="h-5 w-5" />
          </div>
          <div>
            <p className="font-semibold text-dark-900">Connected to backend catalog</p>
            <p className="mt-1 text-sm text-dark-500">
              This page now reads product data from the new CodeIgniter e-commerce API instead of mock data.
            </p>
          </div>
        </div>
      </div>

      <FilterGrid>
        <FormInput
          control={control}
          name="keywords"
          type="text"
          icon={Search}
          placeholder="Search by product name, SKU, slug, or category..."
        />

        <FormInput
          control={control}
          name="category_id"
          type="dropdown"
          placeholder="Filter by category"
          dropdownOptions={[ALL_CATEGORY_OPTION]}
          loadDropdownOptions={categoryDropdownLoader}
        />
      </FilterGrid>

      <Table
        columns={productColumns}
        data={data?.data ?? []}
        loading={isPending}
        emptyText="No products available yet."
        pagination={data?.pagination}
        onPageChange={setPage}
      />

      <ProductFormModal
        open={productFormMode !== null}
        mode={productFormMode ?? "create"}
        productId={selectedProductId}
        onClose={() => {
          setSelectedProductId(null);
          setProductFormMode(null);
        }}
      />

      <ProductDeleteModal
        open={deleteTarget !== null}
        target={deleteTarget}
        onClose={() => setDeleteTarget(null)}
      />
    </>
  );
};
