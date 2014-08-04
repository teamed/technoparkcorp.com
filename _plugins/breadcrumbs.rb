module Tpc
  module Breadcrumbs
    def breadcrumbs(page)
      link = ''
      list = page['permalink'].split('/').map { |item|
        link += "/#{item}"
        "<a href='#{link}'>#{item}</a>"
      }
      list.join(' &gt; ')
    end
  end
end

Liquid::Template.register_filter(Tpc::Breadcrumbs)
