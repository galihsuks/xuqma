import {
  BookOpen,
  Boxes,
  Building2,
  ChartColumn,
  Circle,
  ClipboardList,
  FilePenLine,
  FileBarChart2,
  FolderKanban,
  FolderTree,
  Home,
  LayoutDashboard,
  Logs,
  PackageSearch,
  ReceiptText,
  Settings,
  Shield,
  SlidersHorizontal,
  SquareStack,
  Users,
  type LucideIcon,
} from "lucide-react";
import type { DropdownOption } from "../interfaces/dropdown";

export const menuIconMap: Record<string, LucideIcon> = {
  LayoutDashboard,
  Boxes,
  Users,
  Shield,
  Settings,
  BookOpen,
  ChartColumn,
  Home,
  Logs,
  SlidersHorizontal,
  SquareStack,
  ReceiptText,
  FileBarChart2,
  ClipboardList,
  Building2,
  FolderKanban,
  FolderTree,
  FilePenLine,
  PackageSearch,
};

export const menuIconOptions: DropdownOption[] = Object.keys(menuIconMap)
  .sort((first, second) => first.localeCompare(second))
  .map((iconName) => ({
    value: iconName,
    label: iconName,
  }));

export const resolveMenuIcon = (iconName?: string | null, fallback: LucideIcon = Circle): LucideIcon => {
  if (!iconName) return fallback;
  return menuIconMap[iconName] ?? fallback;
};
