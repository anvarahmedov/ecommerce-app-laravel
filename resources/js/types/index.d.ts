import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    stripe_account_active: boolean;
    vendor: {
      status: string,
      status_label: string,
      store_name: string;
      store_address: string;
      cover_image: string;
    }
}

export type VariationTypeOption = {
  id: number;
  name: string;
  images: Image[];
  type: variationType
}

export type variationType = {
  id:number;
  name:string;
  type: 'Select' | 'Radio' | 'Image';
  options: VariationTypeOption[]
}

export type Product = {
    id: number;
    title: string;
    slug: string;
    price: number;
    quantity: number;
    image: string;
    images: Image[];
    short_description: string;
    description: string;
    user: {
      id: number;
      name: string;
    };
    department: {
      id: number;
      name: string;
    };
    variationTypes: variationType[],
    variations: Array<{
      id: number;
      variation_type_options_ids:number[];
      quantity:number;
      price:number;
    }>
}

export type Image = {
  id: number;
  thumb:string;
  small:string;
  large:string;
}

export type CartItem = {
  id: number;
  product_id: number;
  title: string;
  slug: string;
  price: number;
  quantity: number;
  image: string;
  option_ids: Record<string, number>;
  options: VariationTypeOption[];
}

export type GroupedCartItems = {
  user: User;
  items: CartItem[];
  totalPrice: number;
  totalQuantity: number;
}

export type PaginationProps<T> = {
  data: Array<T>;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
   csrf_token: string;
   error:string;
   success:{
    message:string,
    time:number
   };
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
    totalPrice: number;
    totalQuantity: number;
    miniCartItems: CartItem[];
};

export type OrderItem = {
  id: number;
  quantity: number;
  price: number;
  variation_type_options_ids: number[];
  product: {
    id: number;
    title: string;
    slug: string;
    description: string;
  }
}

export type Order = {
  id: number;
  total_price: number;
  status: string;
  created_at: string;
  vendorUser: {
    id:string;
    name:string;
    email:string;
    store_name:string;
    store_address:string;
  }
  orderItems: OrderItem[];
}
