export interface Room {
  id: number;
  name: string;
  price: number;
  image: string;
  room_image?: string;
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