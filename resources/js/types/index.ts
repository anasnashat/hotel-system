export interface Room {
  id: number;
  name: string;
  number: number;
  price: number;
  is_available: boolean;
  first_image_url?: string;
  description?: string;
  features?: string[];
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

export interface User {
  id: number;
  name: string;
  email: string;
  roles: { name: string }[];
}

export interface SharedData {
  auth: {
    user: User | null;
  };
  [key: string]: any;
}
