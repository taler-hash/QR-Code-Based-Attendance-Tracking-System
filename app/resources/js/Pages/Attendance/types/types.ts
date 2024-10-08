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
    section: number,
    date: string
}

export interface FormTypes {
    id?:number,
    username?: string,
    password?: string|null,
    name: string,
    sections: [],
    status: string
}

export interface sectionOptionTypes {
    section: number,
    sectionDetails: { section: string}
}
