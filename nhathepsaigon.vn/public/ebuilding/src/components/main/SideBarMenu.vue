<template>
  <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

      <div class="menu_section">
        <ul class="nav side-menu">
          <li v-for="item in acl" :class="item.active ? 'current-page' : ''" v-if="item.enable">
            <router-link :to="`${item.path}`" v-if="item.kind === 'F'"><i :class="item.clazz"></i> {{item.name}}</router-link>
            <a v-if="item.kind === 'D'" @click="selectMenu(item.id)"><i :class="item.clazz"></i> {{item.name}} <span class="fa fa-chevron-down"></span></a>
            <collapse v-model="item.show" tag="ul" class="nav child_menu" v-if="item.kind === 'D'">
              <li v-for="sub in item.children">
                <router-link :to="`${sub.path}`" v-if="sub.kind === 'F'"> {{sub.name}}</router-link>
              </li>
            </collapse>
          </li>
        </ul>
      </div>

    </div>
</template>

<script>
  export default {
    name: 'SideBarMenu',
    data (){
      return {
        show: false,
        acl: [
          {id: 'Dashboard', name: 'Dashboard', clazz: 'fa fa-dashboard', kind: 'F', path: '/', server: 'building', active: true, enable: true},
          {id: 'can-ho', name: 'Căn hộ', clazz: 'fa fa-home', kind: 'F', path: '/can-ho', server: '', active: false, enable: true},
          {id: 'cu-dan', name: 'Cư dân', clazz: 'fa fa-user', kind: 'F', path: '/cu-dan', server: '', active: false, enable: true},
          {id: 'dich-vu', name: 'Dịch vụ', clazz: 'fa fa-laptop', kind: 'F', path: '/dich-vu', server: '', active: false, enable: true},
          {id: 'thong-bao-phi', name: 'Thông báo phí', clazz: 'fa fa-info', kind: 'F', path: '/thong-bao-phi', server: '', active: false, enable: true},
          {id: 'thanh-toan', name: 'Thanh toán', clazz: 'fa fa-credit-card', kind: 'F', path: '/thanh-toan', server: '', active: false, enable: true},
          {id: 'giao-tiep', name: 'Giao tiếp', clazz: 'fa fa-wechat', kind: 'D', path: '', server: '', active: false, enable: true, show: false, children: [
              {id: 'thong-bao-chung', name: 'Thông báo chung', clazz: '', kind: 'F', path: '/thong-bao-chung', server: '', active: false, enable: true},
              {id: 'y-kien-cu-dan', name: 'Ý kiến cư dân', clazz: '', kind: 'F', path: '/y-kien-cu-dan', server: '', active: false, enable: true},
            ]
          },
          {id: 'nhan-vien', name: 'Nhân viên', clazz: 'fa fa-users', kind: 'F', path: '/nhan-vien', server: '', active: false, enable: true},
          {id: 'cong-viec-noi-bo', name: 'Công việc nội bộ', clazz: 'fa fa-sitemap', kind: 'F', path: '/cong-viec-noi-bo', server: '', active: false, enable: true},
          {id: 'trang-thiet-bi-vat-tu', name: 'Trang thiết bị vật tư', clazz: 'fa fa-desktop', kind: 'F', path: '/cong-viec-noi-bo', server: '', active: false, enable: true},
          {id: 'bao-cao', name: 'Báo cáo', clazz: 'fa fa-book', kind: 'D', path: '', server: '', active: false, enable: true, show: false, children: [
              {id: 'bao-cao-so-quy', name: 'Sổ quỹ', clazz: '', kind: 'F', path: '/bao-cao-so-quy', server: '', active: false, enable: true},
              {id: 'bao-cao-tong-thu-dich-vu', name: 'Tổng thu dịch vụ', clazz: '', kind: 'F', path: '/bao-cao-tong-thu-dich-vu', server: '', active: false, enable: true},
              {id: 'bao-cao-phieu-thu', name: 'Phiếu thu', clazz: '', kind: 'F', path: '/bao-cao-phieu-thu', server: '', active: false, enable: true},
              {id: 'bao-cao-phieu-chi', name: 'Phiếu chi', clazz: '', kind: 'F', path: '/bao-cao-phieu-chi', server: '', active: false, enable: true},
              {id: 'bao-cao-cong-no', name: 'Công nợ', clazz: '', kind: 'F', path: '/bao-cao-cong-no', server: '', active: false, enable: true},
              {id: 'bao-cao-tai-lieu', name: 'Tài liệu', clazz: '', kind: 'F', path: '/bao-cao-tai-lieu', server: '', active: false, enable: true},
              {id: 'bao-cao-tai-khoan', name: 'Tài khoản', clazz: '', kind: 'F', path: '/bao-cao-tai-khoan', server: '', active: false, enable: true}
            ]
          },
          {id: 'cau-hinh-he-thong', name: 'Cấu hình hệ thống', clazz: 'fa fa-cog', kind: 'D', path: '', server: '', active: false, enable: true, show: false, children: [
              {id: 'cau-hinh-he-thong-khoi', name: 'Khối', clazz: '', kind: 'F', path: '/cau-hinh-he-thong-khoi', server: '', active: false, enable: true},
              {id: 'cau-hinh-he-thong-tang', name: 'Tầng', clazz: '', kind: 'F', path: '/cau-hinh-he-thong-tang', server: '', active: false, enable: true},
              {id: 'cau-hinh-he-thong-can-ho', name: 'Căn hộ', clazz: '', kind: 'F', path: '/cau-hinh-he-thong-can-ho', server: '', active: false, enable: true},
              {id: 'cau-hinh-he-thong-dich-vu', name: 'Dịch vụ', clazz: '', kind: 'F', path: '/cau-hinh-he-thong-dich-vu', server: '', active: false, enable: true},
              {id: 'cau-hinh-he-thong-chuc-vu', name: 'Chức vụ', clazz: '', kind: 'F', path: '/cau-hinh-he-thong-chuc-vu', server: '', active: false, enable: true},
              {id: 'cau-hinh-he-thong-phong-ban', name: 'Phòng ban', clazz: '', kind: 'F', path: '/cau-hinh-he-thong-phong-ban', server: '', active: false, enable: true},
              {id: 'cau-hinh-he-thong-nhan', name: 'Nhãn', clazz: '', kind: 'F', path: '/cau-hinh-he-thong-nhan', server: '', active: false, enable: true},
              {id: 'cau-hinh-he-thong-mau', name: 'Mẫu', clazz: '', kind: 'F', path: '/cau-hinh-he-thong-mau', server: '', active: false, enable: true},
              {id: 'cau-hinh-he-thong-tai-khoan', name: 'Tài khoản', clazz: '', kind: 'F', path: '/cau-hinh-he-thong-tai-khoan', server: '', active: false, enable: true}
            ]
          }
        ]
      }
    },
    mounted (){

    },
    methods: {
      selectMenu(id){
        this.acl.forEach(item => {
          if(item.kind === 'D'){
            if(item.id === id) item.show = !item.show
            else item.show = false
          }
        })
      }
    }
  }
</script>

<style scoped>
.child_menu{}
</style>
