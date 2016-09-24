feature 'Localization' do

    scenario 'French' do
        prefer_language('fr-FR;q=0.9,fr;q=0.8,en;q=0.7')
        visit '/works/'
        find("a",:text=>'Ajouter une œuvre').trigger(:click)
        expect(page).to have_content 'Œuvre originale'
    end

    scenario 'English' do
        prefer_language('en-au,en-us,en,fr')
        visit '/works/'
        find("a",:text=>'Add a work').trigger(:click)
        expect(page).to have_content 'Original work'
    end

    scenario 'Portugese' do
        prefer_language('pt-br,en-us,en')
        visit '/works/'
        find("a",:text=>'Adicionar uma obra').trigger(:click)
        expect(page).to have_content 'Obra original'
    end

    scenario 'Spanish' do
        prefer_language('es-ar,es-es,en-us,en')
        visit '/works/'
        find("a",:text=>'Añadir una obra').trigger(:click)
        expect(page).to have_content 'Obra original'
    end

    scenario 'Chinese' do
        prefer_language('zh-cn,en-us,en')
        visit '/works/'
        find("a",:text=>'添加作品').trigger(:click)
        expect(page).to have_content '原始作品'
    end

end
