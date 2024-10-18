export interface SortTypes {
    sortOrder: number|null,
    sortField: string
}

export interface PageTypes {
    page: number,
    rows: number
}

export interface FilterTypes {
    page: number,
    sortBy: string,
    sortType: number|null,
    rows: number,
    filter: any
}

export interface FormTypes {
    section: string|null,
    description: string|null,
    time_in: any,
    time_out: any
}
