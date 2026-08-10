<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MenuGroupModel;
use App\Models\MenuModel;

class Menus extends BaseController
{
    protected MenuModel $menuModel;
    protected MenuGroupModel $menuGroupModel;

    public function __construct()
    {
        $this->menuModel      = new MenuModel();
        $this->menuGroupModel = new MenuGroupModel();
    }

    public function index()
    {
        $groupId = $this->request->getGet('group_id');

        if (empty($groupId)) {
            $firstGroup = $this->menuGroupModel->where('deleted_at', null)->orderBy('id', 'ASC')->first();
            if ($firstGroup) {
                return redirect()->to(base_url('admin/menus?group_id=' . $firstGroup->id));
            }
        }

        $showTrash = (bool) $this->request->getGet('trash');

        $group = $this->menuGroupModel->find($groupId);
        if (! $group) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Grup menu tidak ditemukan.');
        }

        $data = [
            'list'        => $this->menuModel->getFlatTree((int) $groupId, null, 0, $showTrash),
            'show_trash'  => $showTrash,
            'group'       => $group,
            'group_id'    => $groupId,
            'menu_groups' => $this->menuGroupModel->findAll(),
            'title'       => 'Kelola Item Navigasi: ' . $group->name . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => [
                'Menu Builder'                             => 'admin/menu-groups',
                $group->name                               => 'admin/menus?group_id=' . $groupId,
                ($showTrash ? 'Sampah' : 'Daftar Item') => '',
            ],
        ];

        return view('admin/menus/index', $data);
    }

    public function create()
    {
        $groupId = $this->request->getGet('group_id');
        $group   = $this->menuGroupModel->find($groupId);
        if (! $group) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Grup menu tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $linkType = $this->request->getPost('link_type');
            $url      = ($linkType === 'internal') ? $this->request->getPost('internal_url') : $this->request->getPost('url');

            if (empty($url)) {
                session()->setFlashdata('error', ($linkType === 'internal') ? 'URL Internal wajib dipilih.' : 'URL External wajib diisi.');
                return $this->_renderCreateForm($group, (int) $groupId);
            }

            $parentId = $this->request->getPost('parent_id');

            $postData = [
                'group_id'    => $groupId,
                'title'       => $this->request->getPost('title'),
                'url'         => $url,
                'parent_id'   => ! empty($parentId) ? (int) $parentId : null,
                'order_num'   => $this->request->getPost('order_num'),
                'target'      => $this->request->getPost('target'),
                'status'      => $this->request->getPost('status'),
                'icon'        => $this->request->getPost('icon'),
                'description' => $this->request->getPost('description'),
            ];

            if (! $this->menuModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->menuModel->errors()));
                return $this->_renderCreateForm($group, (int) $groupId);
            }

            $insertId = $this->menuModel->insert($postData);

            if ($insertId) {
                $this->logActivity('Item Menu', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Item Menu berhasil dibuat.');
                return redirect()->to(base_url('admin/menus?group_id=' . $groupId));
            }
        }

        return $this->_renderCreateForm($group, (int) $groupId);
    }

    public function edit($id)
    {
        $row = $this->menuModel->withDeleted()->find($id);
        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Item menu tidak ditemukan.');
        }

        $groupId = $row->group_id;
        $group   = $this->menuGroupModel->find($groupId);

        if ($this->request->getMethod() === 'post') {
            $linkType = $this->request->getPost('link_type');
            $url      = ($linkType === 'internal') ? $this->request->getPost('internal_url') : $this->request->getPost('url');

            if (empty($url)) {
                session()->setFlashdata('error', ($linkType === 'internal') ? 'URL Internal wajib dipilih.' : 'URL External wajib diisi.');
                return $this->_renderEditForm((int) $id, $row, $group);
            }

            $parentId = $this->request->getPost('parent_id');
            $parentId = ! empty($parentId) ? (int) $parentId : null;

            if (! empty($parentId)) {
                if ($parentId === (int) $id) {
                    session()->setFlashdata('error', 'Gagal: Parent menu tidak boleh menunjuk dirinya sendiri.');
                    return $this->_renderEditForm((int) $id, $row, $group);
                }
                if ($this->isCircularParent((int) $id, $parentId)) {
                    session()->setFlashdata('error', 'Gagal: Terjadi circular parent loop. Parent menu tidak boleh merupakan keturunan dari item ini.');
                    return $this->_renderEditForm((int) $id, $row, $group);
                }
            }

            $postData = [
                'id'          => $id,
                'group_id'    => $groupId,
                'title'       => $this->request->getPost('title'),
                'url'         => $url,
                'parent_id'   => $parentId,
                'order_num'   => $this->request->getPost('order_num'),
                'target'      => $this->request->getPost('target'),
                'status'      => $this->request->getPost('status'),
                'icon'        => $this->request->getPost('icon'),
                'description' => $this->request->getPost('description'),
            ];

            if (! $this->menuModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->menuModel->errors()));
                return $this->_renderEditForm((int) $id, $row, $group);
            }

            $this->menuModel->update($id, $postData);

            $this->logActivity('Item Menu', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Item Menu berhasil diperbarui.');
            return redirect()->to(base_url('admin/menus?group_id=' . $groupId));
        }

        return $this->_renderEditForm((int) $id, $row, $group);
    }

    public function delete($id)
    {
        $row = $this->menuModel->find($id);
        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Item menu tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->menuModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Item Menu', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Item Menu berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/menus?group_id=' . $row->group_id));
    }

    public function restore($id)
    {
        $row = $this->menuModel->onlyDeleted()->find($id);
        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Item menu tidak ditemukan di tempat sampah.');
        }

        $this->menuModel->restoreWithUser((int) $id);
        $this->logActivity('Item Menu', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Item Menu berhasil dipulihkan.');
        return redirect()->to(base_url('admin/menus?group_id=' . $row->group_id . '&trash=1'));
    }

    public function force_delete($id)
    {
        $userId = session()->get('user_id');
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');

        if (! $rbac->is_super_admin((int) $userId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Hanya Super Admin yang dapat menghapus item secara permanen.']);
            }
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Hanya Super Admin yang dapat menghapus item secara permanen.']));
        }

        $row = $this->menuModel->onlyDeleted()->find($id);
        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Item menu tidak ditemukan.');
        }

        $this->menuModel->delete($id, true); // Hard delete
        $this->logActivity('Item Menu', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Item Menu berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/menus?group_id=' . $row->group_id . '&trash=1'));
    }

    public function toggle_status($id)
    {
        $item = $this->menuModel->find($id);
        if (! $item) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Item menu tidak ditemukan.');
        }

        $newStatus = ($item->status === 'published') ? 'draft' : 'published';
        $this->menuModel->update($id, ['status' => $newStatus]);

        $actionName = ($newStatus === 'published') ? 'publish' : 'unpublish';
        $this->logActivity('Item Menu', $actionName, json_encode($item), json_encode(['status' => $newStatus]));

        session()->setFlashdata('success', 'Item Menu berhasil ' . ($newStatus === 'published' ? 'diterbitkan' : 'diarsipkan') . '.');
        return redirect()->to(base_url('admin/menus?group_id=' . $item->group_id));
    }

    public function save_order()
    {
        $orders  = $this->request->getPost('orders');
        $groupId = $this->request->getPost('group_id');

        if (is_array($orders)) {
            foreach ($orders as $id => $orderNum) {
                $id       = (int) $id;
                $orderNum = (int) $orderNum;

                $item = $this->menuModel->find($id);
                if ($item && (int) $item->order_num !== $orderNum) {
                    $this->menuModel->update($id, ['order_num' => $orderNum]);
                    $this->logActivity('Item Menu', 'sort', json_encode($item), json_encode(['order_num' => $orderNum]));
                }
            }
            session()->setFlashdata('success', 'Urutan menu berhasil diperbarui.');
        }

        return redirect()->to(base_url('admin/menus?group_id=' . $groupId));
    }

    private function _renderCreateForm($group, int $groupId)
    {
        $data = [
            'group'         => $group,
            'group_id'      => $groupId,
            'title'         => 'Tambah Item Menu - ' . $group->name,
            'breadcrumbs'   => [
                'Menu Builder' => 'admin/menu-groups',
                $group->name   => 'admin/menus?group_id=' . $groupId,
                'Tambah'       => '',
            ],
            'parents'       => $this->getSafeParents($groupId),
            'internal_data' => $this->getInternalLinkData(),
        ];

        return view('admin/menus/create', $data);
    }

    private function _renderEditForm(int $id, $row, $group)
    {
        $data = [
            'row'           => $row,
            'group'         => $group,
            'group_id'      => $row->group_id,
            'title'         => 'Edit Item Menu - ' . $row->title,
            'breadcrumbs'   => [
                'Menu Builder' => 'admin/menu-groups',
                $group->name   => 'admin/menus?group_id=' . $row->group_id,
                'Edit'         => '',
            ],
            'parents'       => $this->getSafeParents((int) $row->group_id, $id),
            'internal_data' => $this->getInternalLinkData(),
        ];

        return view('admin/menus/edit', $data);
    }

    private function isCircularParent(int $id, int $parentId): bool
    {
        if ($id === $parentId) {
            return true;
        }

        $current = $parentId;
        $visited = [];

        while (! empty($current)) {
            if ($current === $id) {
                return true;
            }
            if (in_array($current, $visited, true)) {
                return true;
            }
            $visited[] = $current;

            $row = $this->menuModel->builder()->select('parent_id')->where('id', $current)->get()->getRow();
            if (! $row || empty($row->parent_id)) {
                break;
            }
            $current = (int) $row->parent_id;
        }

        return false;
    }

    private function getSafeParents(int $groupId, ?int $excludeId = null): array
    {
        $allItems = $this->menuModel->getFlatTree($groupId);
        if (empty($excludeId)) {
            return $allItems;
        }

        $descendants = $this->getDescendantIds($excludeId);
        $excludeIds  = array_merge([$excludeId], $descendants);

        $safeItems = [];
        foreach ($allItems as $item) {
            if (! in_array((int) $item->id, $excludeIds, true)) {
                $safeItems[] = $item;
            }
        }

        return $safeItems;
    }

    private function getDescendantIds(int $id): array
    {
        $descendants = [];
        $rows        = $this->menuModel->builder()->select('id')->where('parent_id', $id)->where('deleted_at', null)->get()->getResult();

        foreach ($rows as $row) {
            $descendants[] = (int) $row->id;
            $descendants   = array_merge($descendants, $this->getDescendantIds((int) $row->id));
        }

        return $descendants;
    }

    private function getInternalLinkData(): array
    {
        $db = \Config\Database::connect();

        return [
            'pages'            => $db->table('pages')->where('deleted_at', null)->get()->getResult(),
            'posts'            => $db->table('posts')->where('deleted_at', null)->where('status', 'published')->get()->getResult(),
            'categories'       => $db->table('categories')->where('deleted_at', null)->get()->getResult(),
            'tags'             => $db->table('tags')->where('deleted_at', null)->get()->getResult(),
            'achievements'     => $db->table('achievements')->where('deleted_at', null)->get()->getResult(),
            'agendas'          => $db->table('agendas')->where('deleted_at', null)->get()->getResult(),
            'extracurriculars' => $db->table('extracurriculars')->where('deleted_at', null)->get()->getResult(),
        ];
    }

    protected function logActivity(string $module, string $action, ?string $oldValue = null, ?string $newValue = null)
    {
        $userId = session()->get('user_id');
        if (class_exists('\Logger')) {
            $logger = new \Logger();
            $logger->log($userId, $module, $action, $oldValue, $newValue);
        }
    }
}
