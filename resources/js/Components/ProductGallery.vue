<template>
<div class="gallery">

    <!-- imagen principal -->
    <div class="main-image">
        <img :src="selectedImage" class="main-img"/>
    </div>

    <!-- thumbnails -->
    <div class="thumbs">
        <img
            v-for="img in images"
            :key="img.id"
            :src="img.image_path"
            class="thumb"
            @click="selectedImage = img.image_path"
        />
    </div>

</div>
</template>

<script>
export default {

props:["images"],

data(){
return{
    selectedImage:null
}
},

mounted(){
    if(this.images.length){
        this.selectedImage = this.images[0].image_path
    }
}

}
</script>

<style>

.gallery{
width:400px;
}

.main-img{
width:100%;
border-radius:10px;
cursor:zoom-in;
}

.thumbs{
display:flex;
gap:10px;
margin-top:10px;
}

.thumb{
width:60px;
height:60px;
object-fit:cover;
cursor:pointer;
border:2px solid transparent;
}

.thumb:hover{
border:2px solid orange;
}

</style>