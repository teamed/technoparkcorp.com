module Tpc
  module Tags
    class Breadcrumbs < Liquid::Tag
      def render(context)
        page = context['page']
        link = ''
        page['permalink'].split('/').map { |item|
          link += "/#{item}"
          post = find(link[1, link.length], context)
          if post
            "<a href='/#{post['permalink']}'>#{post['label']}</a>"
          else
            nil
          end
        }.compact.join(' &gt; ')
      end
      private
      def find(name, context)
        site = context.registers[:site]
        site.posts.each do |post|
          if post.permalink == name
            return post
          end
        end
        nil
      end
    end
  end
end

Liquid::Template.register_tag('breadcrumbs', Tpc::Tags::Breadcrumbs)
