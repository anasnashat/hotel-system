export interface Room {
  id: number;
  name: string;
  price: number;
  image: string;
  description?: string;
}

export interface NavItem {
  title: string;
  href: string;
  icon: any;
}

export interface BreadcrumbItemType {
  label: string;
  href?: string;
} 