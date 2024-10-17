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
    name?: string,
    username: string,
    password?: string|null,
    first_name?: string,
    last_name?: string,
    sections: {section:string, id: number}[],
    status: string
}

export interface sectionOptionTypes {
    section: number,
    sectionDetails: { section: string}
}
