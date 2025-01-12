import { CartItem } from "./types";

export const arraysAreEqual = (arr1: any[], arr2: any[]) => {
  if (arr1.length !== arr2.length) {
    return false;
  }
  return arr1.every((value, index) => value === arr2[index]);
};

export const productRoute = (
  (item: CartItem) => {
    const params = new URLSearchParams();
    Object.entries(item.option_ids).forEach(([typeId, optionId]) => {
      params.append(`options[${typeId}]`, optionId + '')
    })
    //console.log(params.toString());
    console.log(item.option_ids);

    return route('product.show', item.slug) + '?' + params.toString();
  }
)
