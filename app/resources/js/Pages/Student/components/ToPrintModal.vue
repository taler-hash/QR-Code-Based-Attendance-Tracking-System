<template>
    <Dialog v-model:visible="visible" modal header="Qr Codes">
        <template #header>
            <div class="flex items-center space-x-2">
                <p>QR Codes</p>
                <Button label="Submit" @click="print" />
            </div>
        </template>
        <div class="lg:bg-gray-300 p-1">
            <div class="hidden lg:block" :class="{'hidden': !visible}">
                <div v-for="(value, key) of groupedSection " :key="key" :ref="el => printables[key] = el"
                    class="w-[8.27in] h-[11.69in] bg-white  border">
                    <div class="px-2 pt-5">
                        <p class="font-medium text-2xl">{{ key }}</p>
                    </div>
                    <div class="w-full grid grid-cols-10 items-center p-[0.5in]">
                        <div v-for="student in value.data"
                            class="border border-dashed border-gray-500 m-0.5 p-1 text-center">
                            <img :src="student.qrCode" alt="" class="object-fill">
                            <p class="truncate text-[9px] font-medium">{{ student.name }}</p>
                            <p class="truncate text-[9px] font-bold">{{ student.section }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:hidden py-10">
                <p class="font-bold">No Preview Available use bigger screen to show</p>
            </div>

        </div>
    </Dialog>
</template>
<script setup lang="ts">
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import { Ref, ref } from 'vue';
import jsPDF from 'jspdf';
import { toCanvas } from 'html-to-image';
import axios from 'axios';
import { useQRCode } from '@vueuse/integrations/useQRCode'
import _ from 'lodash';
import { useToast } from 'primevue/usetoast';


type SectionType = number[]

interface GroupedSectionTypes {
    [section: string]: {
        data: {
            qrCode: string,
            section: string,
            name: string
        }[],
        index: number
    }
}


const visible = ref<boolean>(false)
const printables = ref<{ [index: string]: any }>({})
const groupedSection = ref<GroupedSectionTypes>({})
const toast = useToast()

async function open(sections: any, type: "custom" | "section") {

    if (type === 'section') {
        getStudents(sections).then(() => {
            visible.value = true
        })
    } else if (type === 'custom') {
        groupedSection.value = sections
        visible.value = true
    }

}
function print() {
    let doc = new jsPDF('p', 'in', 'a4', true).deletePage(1);
    let promises: Promise<void>[] = []

    for (let section in groupedSection.value) {
        const element = printables.value[section]
        const promise = toCanvas(element, { quality: 1 })
            .then(function (canvas: HTMLCanvasElement) {
                const canvasURL = canvas.toDataURL('image/svg', 1)

                // Get the image width and height based on the canvas
                const imgWidth = canvas.width * 0.0104; // Convert px to inches for jsPDF
                const imgHeight = canvas.height * 0.0104;
                doc.addPage('a4', 'p')
                doc.addImage(canvasURL, 'JPEG', 0, 0, imgWidth, imgHeight)
            })
        promises[groupedSection.value[section].index] = promise
    }

    Promise.all(promises).then(() => {
        const pdfName = Object.keys(groupedSection.value as GroupedSectionTypes).toString()
        doc.save(pdfName);
        toast.add({ severity: 'success', summary: 'Success', detail: `PDF Generated Successfully ${pdfName}.pdf`, life: 6000 });
    })
}

async function getStudents(sections: SectionType) {
    const result = await axios.get(route('sections.studentsundersectionof', { sections: sections }))
    if (result.status === 200) {
        dividePage(result.data)
    }
}

function dividePage(data: []) {
    const pages: { [section: string]: [] } = {}
    let indices = 0

    data.map((i: { users: [], section: string }) => {
        const chunked = _.chunk(i.users, 80)

        chunked.map((v, index) => {
            const generatedQrCode: any = generateQrCode(v as [], i.section)
            pages[`${i.section}_${index + 1}`] = {
                data: generatedQrCode,
                index: indices
            }

            indices++
        })
    })

    groupedSection.value = pages
}

function generateQrCode(data: [], section: string) {
    return data.map((i: { qrCode: Ref<string, string>, uuid: string, section: string }) => {
        i.qrCode = useQRCode(i.uuid)
        i.section = section
        return i
    })
}

defineExpose({
    open
})

</script>