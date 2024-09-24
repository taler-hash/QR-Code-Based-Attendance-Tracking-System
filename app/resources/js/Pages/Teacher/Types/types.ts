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
    username: string,
    password?: string|null,
    name: string,
    sections: [],
    status: string
}

export interface sectionOptionTypes {
    section: number,
    sectionDetails: { section: string}
}
